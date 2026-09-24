<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    // Pantalla principal del KDS
    public function index()
    {
        // Buscamos órdenes que tengan platos pendientes o cocinando
        // Ordenamos por las más antiguas primero (FIFO - First In, First Out)
        $orders = Order::whereHas('details', function($q) {
                            $q->whereIn('status', ['pending', 'cooking']);
                        })
                        ->with(['table', 'details' => function($q) {
                            $q->whereIn('status', ['pending', 'cooking']);
                        }])
                        ->orderBy('created_at', 'asc')
                        ->get();

        return view('kitchen.index', compact('orders'));
    }

    // Devuelve los pedidos activos para actualizar el KDS sin recargar la página
    public function orders()
    {
        $orders = Order::whereHas('details', function ($q) {
                $q->whereIn('status', ['pending', 'cooking']);
            })
            ->with([
                'table',
                'details' => function ($q) {
                    $q->whereIn('status', ['pending', 'cooking'])
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
    // Avanzar estado del plato y sincronizar automáticamente el Delivery
    public function updateStatus(OrderDetail $detail)
    {
        if ($detail->status === 'pending') {
            $detail->update(['status' => 'cooking']);
        } elseif ($detail->status === 'cooking') {
            $detail->update(['status' => 'served']);
        }

        $order = $detail->order()->with('details')->first();

        if ($order) {
            $delivery = $order->delivery;

            if ($delivery && !in_array($delivery->status, ['delivered', 'cancelled'])) {
                $hasCookingItems = $order->details
                    ->contains(fn ($item) => $item->status === 'cooking');

                $allServed = $order->details->isNotEmpty()
                    && $order->details->every(fn ($item) => $item->status === 'served');

                if ($allServed) {
                    // Delivery: En camino
                    // Recojo en local: Listo para recoger
                    $delivery->update(['status' => 'on_way']);
                } elseif ($hasCookingItems) {
                    $delivery->update(['status' => 'preparing']);
                }
            }
        }

        return redirect()->route('kitchen.index');
    }
}