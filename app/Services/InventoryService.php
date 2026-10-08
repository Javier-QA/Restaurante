<?php

namespace App\Services;

use App\Models\InventoryLog;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    /** Called inside the payment transaction; aggregate shared recipe ingredients before locking. */
    public function consume(iterable $details, string $note): void
    {
        if (DB::transactionLevel() === 0) {
            throw new \LogicException('El inventario debe actualizarse dentro de una transacción.');
        }
        $requirements = [];
        foreach ($details as $detail) {
            $product = Product::with('ingredients')->findOrFail($detail->product_id);
            if ($product->ingredients->isNotEmpty()) {
                foreach ($product->ingredients as $ingredient) {
                    $requirements[$ingredient->id] = ($requirements[$ingredient->id] ?? 0)
                        + (float) $ingredient->pivot->quantity * (float) $detail->quantity;
                }
            } elseif ($product->controls_stock && $product->stock !== null) {
                $requirements[$product->id] = ($requirements[$product->id] ?? 0) + (float) $detail->quantity;
            }
        }
        ksort($requirements);
        foreach ($requirements as $id => $quantity) {
            $product = Product::whereKey($id)->lockForUpdate()->firstOrFail();
            $old = (float) $product->stock;
            $quantity = round($quantity, 3);
            if ($old + 0.000001 < $quantity) {
                throw ValidationException::withMessages(['stock' => "Stock insuficiente de {$product->name}. Disponible: {$old}. Necesario: {$quantity}."]);
            }
            $new = round($old - $quantity, 3);
            $product->update(['stock' => $new]);
            InventoryLog::create([
                'product_id' => $id, 'user_id' => auth()->id(), 'type' => 'sale',
                'quantity' => -$quantity, 'old_stock' => $old, 'new_stock' => $new, 'note' => $note,
            ]);
        }
    }
}
