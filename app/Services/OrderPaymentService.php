<?php

namespace App\Services;

use App\Models\CashRegister;
use App\Models\Client;
use App\Models\DocumentSeries;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Services\Sunat\SunatConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderPaymentService
{
    public function pay(Request $request, Order $order, bool $split = false): Order
    {
        $rules = [
            'payment_method' => 'required|in:cash,card,yape,plin',
            'received_amount' => 'required_if:payment_method,cash|nullable|numeric|min:0',
            'document_type' => 'required|in:Ticket,Boleta,Factura',
            'client_id' => 'nullable|integer|exists:clients,id',
            'client_name' => 'nullable|string|max:255',
            'client_document' => 'nullable|string|max:11',
        ];
        if ($split) {
            $rules += [
                'selected_items' => 'required|array|min:1',
                'selected_items.*' => 'required|integer|distinct',
                'split_quantities' => 'required|array',
                'split_quantities.*' => 'nullable|integer|min:0',
            ];
        }
        $data = $request->validate($rules);

        return DB::transaction(function () use ($order, $data, $split) {
            // Match the lock order used by cash closing: register, order, details, stock.
            $register = CashRegister::where('status', 'open')->orderBy('id')->lockForUpdate()->first();
            if (! $register) {
                $this->fail('cash_register', 'Debe abrir una caja antes de cobrar.');
            }
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($order->status !== 'pending' || ! $order->table_id) {
                $this->fail('order', 'El pedido ya está cerrado o no pertenece a una mesa.');
            }
            $all = $order->details()->lockForUpdate()->get();
            if ($all->isEmpty()) {
                $this->fail('order', 'No se puede cobrar un pedido vacío.');
            }
            if ($all->contains(fn ($line) => in_array($line->status, ['draft', 'pending'], true))) {
                $this->fail('order', 'Los productos deben iniciar su preparación antes de cobrar.');
            }
            $selected = $split ? $all->whereIn('id', $data['selected_items'])->values() : $all;
            if ($split && $selected->count() !== count($data['selected_items'])) {
                $this->fail('selected_items', 'Selecciona únicamente productos de este pedido.');
            }
            $gross = round($all->sum(fn ($line) => (float) $line->price * $line->quantity), 2);
            $partGross = 0;
            $paidLines = collect();
            foreach ($selected as $line) {
                $qty = $split ? (int) ($data['split_quantities'][$line->id] ?? 0) : (int) $line->quantity;
                if ($qty < 1 || $qty > $line->quantity) {
                    $this->fail('split_quantities', 'La cantidad seleccionada no es válida.');
                }
                $copy = clone $line;
                $copy->quantity = $qty;
                $paidLines->push($copy);
                $partGross += (float) $line->price * $qty;
            }
            $partGross = round($partGross, 2);
            $ratio = $gross > 0 ? $partGross / $gross : 1;
            $discount = round((float) $order->discount * $ratio, 2);
            $tip = round((float) $order->tip * $ratio, 2);
            $total = round(max(0, $partGross - $discount + $tip), 2);
            $received = $data['payment_method'] === 'cash' ? round((float) $data['received_amount'], 2) : $total;
            if ($received < $total) {
                $this->fail('received_amount', 'El monto recibido es menor al total a cobrar.');
            }
            $client = ! empty($data['client_id']) ? Client::findOrFail($data['client_id']) : null;
            $document = trim((string) ($data['client_document'] ?? $client?->document_number ?? ''));
            $name = trim((string) ($data['client_name'] ?? $client?->name ?? $order->client_name ?? 'Público'));
            if ($data['document_type'] === 'Factura' && (! preg_match('/^\d{11}$/', $document) || $name === '' || $name === 'Público')) {
                $this->fail('client_document', 'La factura requiere un RUC de 11 dígitos y razón social.');
            }
            if ($data['document_type'] === 'Boleta' && $document !== '' && ! preg_match('/^\d{8}$/', $document)) {
                $this->fail('client_document', 'El DNI de la boleta debe tener 8 dígitos.');
            }
            $client ??= $document !== '' ? Client::where('document_number', $document)->first() : null;
            $electronic = $data['document_type'] !== 'Ticket';
            $taxBase = $electronic ? round($total / (1 + (new SunatConfig)->igvFactor()), 2) : 0;
            $number = $electronic ? DocumentSeries::next($data['document_type'] === 'Factura' ? 'factura' : 'boleta') : [];
            app(InventoryService::class)->consume($paidLines, 'Venta POS #'.$order->id.($split ? ' (cuenta dividida)' : ''));
            $attributes = [
                'status' => 'completed', 'total' => $total, 'discount' => $discount, 'tip' => $tip,
                'payment_method' => $data['payment_method'], 'received_amount' => $received,
                'change_amount' => round($received - $total, 2), 'document_type' => $data['document_type'],
                'client_id' => $client?->id ?? $order->client_id, 'client_name' => $name ?: 'Público',
                'client_document' => $document ?: null, 'cash_register_id' => $register->id,
                'serie' => $number['serie'] ?? null, 'correlativo' => $number['correlativo'] ?? null,
                'subtotal' => $taxBase, 'total_gravada' => $taxBase, 'igv' => $electronic ? round($total - $taxBase, 2) : 0,
                'paid_at' => now(), 'sunat_status' => $electronic ? 'PENDING' : 'NOT_APPLICABLE',
            ];
            if (! $split) {
                $order->update($attributes);

                return $order;
            }
            $paid = Order::create(['table_id' => $order->table_id, 'user_id' => $order->user_id] + $attributes);
            foreach ($paidLines as $line) {
                $original = $all->firstWhere('id', $line->id);
                if ($line->quantity === (int) $original->quantity) {
                    $original->update(['order_id' => $paid->id]);
                } else {
                    $original->decrement('quantity', $line->quantity);
                    OrderDetail::create([
                        'order_id' => $paid->id, 'product_id' => $line->product_id,
                        'quantity' => $line->quantity, 'price' => $line->price, 'status' => $line->status, 'note' => $line->note,
                    ]);
                }
            }
            if (! $order->details()->exists()) {
                $order->delete();
            } else {
                $remainingGross = round($gross - $partGross, 2);
                $remainingDiscount = round((float) $order->discount - $discount, 2);
                $remainingTip = round((float) $order->tip - $tip, 2);
                $order->update(['discount' => $remainingDiscount, 'tip' => $remainingTip,
                    'total' => round(max(0, $remainingGross - $remainingDiscount + $remainingTip), 2)]);
            }

            return $paid;
        }, 3);
    }

    private function fail(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }
}
