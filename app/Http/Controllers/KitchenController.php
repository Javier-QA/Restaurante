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
        $orders = Order::whereHas('details', function ($q) {
                $q->whereIn('status', ['pending', 'cooking'])
                  ->whereHas('product', function ($p) {
                      $p->where('preparation_area', 'kitchen');
                  });
            })
            ->with([
                'table',
                'details' => function ($q) {
                    $q->whereIn('status', ['pending', 'cooking'])
                      ->whereHas('product', function ($p) {
                          $p->where('preparation_area', 'kitchen');
                      })
                      ->with('product');
                }
            ])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('kitchen.index', compact('orders'));
    }

    // Pedidos activos para actualización automática
    public function orders()
    {
        $orders = Order::whereHas('details', function ($q) {
                $q->whereIn('status', ['pending', 'cooking'])
                  ->whereHas('product', function ($p) {
                      $p->where('preparation_area', 'kitchen');
                  });
            })
            ->with([
                'table',
                'details' => function ($q) {
                    $q->whereIn('status', ['pending', 'cooking'])
                      ->whereHas('product', function ($p) {
                          $p->where('preparation_area', 'kitchen');
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

    // Avanzar estado de un producto de Cocina
    public function updateStatus(Request $request, OrderDetail $detail)
    {
        // Seguridad: Cocina solamente puede modificar productos de Cocina
        if (!$detail->product || $detail->product->preparation_area !== 'kitchen') {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Este producto no pertenece a Cocina.'], 403);
            }
            return redirect()
                ->route('kitchen.index')
                ->with('error', 'Este producto no pertenece a Cocina.');
        }

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
                    && $order->details->every(
                        fn ($item) => $item->status === 'served'
                    );

                if ($allServed) {
                    $delivery->update(['status' => 'on_way']);
                } elseif ($hasCookingItems) {
                    $delivery->update(['status' => 'preparing']);
                }
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Estado actualizado correctamente.', 'status' => $detail->status]);
        }

        return redirect()->route('kitchen.index')->with('success', 'Estado actualizado correctamente.');
    }
}
