<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;

class BarraController extends Controller
{
    public function index()
    {
        $orders = Order::whereHas('details', function ($q) {
                $q->whereIn('status', ['pending', 'cooking'])
                  ->whereHas('product', function ($p) {
                      $p->where('preparation_area', 'barra');
                  });
            })
            ->with([
                'table',
                'details' => function ($q) {
                    $q->whereIn('status', ['pending', 'cooking'])
                      ->whereHas('product', function ($p) {
                          $p->where('preparation_area', 'barra');
                      })
                      ->with('product');
                }
            ])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('barra.index', compact('orders'));
    }

    public function orders()
    {
        $orders = Order::whereHas('details', function ($q) {
                $q->whereIn('status', ['pending', 'cooking'])
                  ->whereHas('product', function ($p) {
                      $p->where('preparation_area', 'barra');
                  });
            })
            ->with([
                'table',
                'details' => function ($q) {
                    $q->whereIn('status', ['pending', 'cooking'])
                      ->whereHas('product', function ($p) {
                          $p->where('preparation_area', 'barra');
                      })
                      ->with('product');
                }
            ])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'orders' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'table' => $order->table
                        ? $order->table->name
                        : 'Para Llevar',
                    'time' => $order->created_at->format('H:i'),
                    'details' => $order->details->map(function ($detail) {
                        return [
                            'id' => $detail->id,
                            'product' => $detail->product?->name ?? 'Producto',
                            'quantity' => $detail->quantity,
                            'status' => $detail->status,
                            'note' => $detail->note,
                        ];
                    })->values(),
                ];
            })->values(),
        ]);
    }

    public function updateStatus(OrderDetail $detail)
    {
        // Seguridad: Barra solamente puede modificar productos de Barra
        if (!$detail->product || $detail->product->preparation_area !== 'barra') {
            return redirect()
                ->route('barra.index')
                ->with('error', 'Este producto no pertenece a Barra.');
        }

        if ($detail->status === 'pending') {
            $detail->update(['status' => 'cooking']);
        } elseif ($detail->status === 'cooking') {
            $detail->update(['status' => 'served']);
        }

        $order = $detail->order()->with('details.product')->first();

        if ($order) {
            $delivery = $order->delivery;

            if ($delivery && !in_array($delivery->status, ['delivered', 'cancelled'])) {

                $hasCookingItems = $order->details
                    ->contains(fn ($item) => $item->status === 'cooking');

                $allServed = $order->details->isNotEmpty()
                    && $order->details->every(fn ($item) => $item->status === 'served');

                if ($allServed) {
                    $delivery->update(['status' => 'on_way']);
                } elseif ($hasCookingItems) {
                    $delivery->update(['status' => 'preparing']);
                }
            }
        }

        return redirect()->route('barra.index');
    }
}