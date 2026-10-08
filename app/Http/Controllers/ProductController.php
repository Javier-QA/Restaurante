<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\InventoryLog;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['search' => 'nullable|string|max:100']);
        $search = trim((string) $request->input('search', ''));
        $products = Product::with('category')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('created_at', 'desc')->orderBy('id', 'desc')
            ->paginate(10)->withQueryString();

        return view('products.index', compact('products', 'search'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // 1. Validación (Incluye el barcode único)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'preparation_area' => 'required|in:kitchen,barra',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'unit' => 'nullable|in:kg,g,lt,ml,und,paq,caja',
            'minimum_stock' => 'nullable|numeric|min:0|max:99999999999',
            'promotional_price' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:50|unique:products,barcode', // <--- NUEVO
            'image' => 'nullable|image|max:2048',
            'stock' => 'nullable|numeric|min:0|max:99999999999',
        ]);

        $data = array_diff_key($validated, array_flip(['ingredients']));

        // 2. Manejo de Imagen
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        // Checkbox de "Disponible en POS" (si no viene, es false)
        $data['is_saleable'] = $request->has('is_saleable');
        $data['controls_stock'] = $request->has('controls_stock');
        $data['is_chef_recommendation'] = $request->has('is_chef_recommendation');
        $data['is_new'] = $request->has('is_new');
        $data['is_active'] = true;
        $data['unit'] = $request->input('unit') ?: 'und';
        $data['minimum_stock'] = $request->input('minimum_stock') ?? 5;
        $data['cost'] = $request->cost ?? 0;

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $data) {
            $product = Product::create($data);
            if ($request->stock > 0) {
                InventoryLog::create([
                    'product_id' => $product->id, 'user_id' => Auth::id(), 'type' => 'entry',
                    'quantity' => $request->stock, 'old_stock' => 0, 'new_stock' => $request->stock,
                    'note' => 'Inventario Inicial',
                ]);
            }
        });

        return redirect()->route('products.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        // Solo productos configurados como insumos.
        // Los productos disponibles para la venta no deben aparecer en la receta.
        $ingredients = Product::where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('is_saleable', false)
            ->orderBy('name')
            ->get();

        return view('products.edit', compact('product', 'categories', 'ingredients'));
    }

    public function update(Request $request, Product $product)
    {
        // 1. Validación (Barcode único excepto para este producto)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'preparation_area' => 'required|in:kitchen,barra',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'unit' => 'nullable|in:kg,g,lt,ml,und,paq,caja',
            'minimum_stock' => 'nullable|numeric|min:0|max:99999999999',
            'promotional_price' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:50|unique:products,barcode,'.$product->id, // <--- NUEVO
            'image' => 'nullable|image|max:2048',
            'ingredients' => 'nullable|array',
            'ingredients.*' => 'nullable|numeric|min:0|max:999999',
        ]);

        foreach ($request->input('ingredients', []) as $id => $qty) {
            if ((float) $qty > 0 && ((int) $id === $product->id || ! Product::whereKey($id)->where('is_active', true)->where('is_saleable', false)->exists())) {
                throw \Illuminate\Validation\ValidationException::withMessages(['ingredients' => 'La receta solo puede incluir insumos activos.']);
            }
        }
        $data = array_diff_key($validated, array_flip(['ingredients']));

        // Once assigned, units cannot be relabelled without converting stock and recipes.
        if ($product->unit && $request->filled('unit') && $request->unit !== $product->unit) {
            throw \Illuminate\Validation\ValidationException::withMessages(['unit' => 'La unidad ya está asignada. Cree un nuevo insumo para otra unidad; cambiarla alteraría stock, costos y recetas.']);
        }
        if (!$request->filled('unit')) { unset($data['unit']); }
        if (!$request->filled('minimum_stock')) { unset($data['minimum_stock']); }
        $oldImage = $product->image;
        $newImage = null;
        if ($request->hasFile('image')) {
            $newImage = $request->file('image')->store('products', 'public');
            $data['image'] = $newImage;
        }

        $data['is_saleable'] = $request->has('is_saleable');
        $data['controls_stock'] = $request->has('controls_stock');
        $data['is_chef_recommendation'] = $request->has('is_chef_recommendation');
        $data['is_new'] = $request->has('is_new');
        $data['cost'] = $request->cost ?? 0;

        // Save product and recipe together after validating allowed ingredients.
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($product, $request, $data) {
                $product->update($data);

                // Actualizar receta/insumos
                // Si no se envían ingredientes, se elimina la receta actual.
                $syncData = [];
                foreach ($request->input('ingredients', []) as $id => $qty) {
                    if ((float) $qty > 0) {
                        if ((int) $id === $product->id || ! Product::whereKey($id)->where('is_active', true)->where('is_saleable', false)->exists()) {
                            throw \Illuminate\Validation\ValidationException::withMessages(['ingredients' => 'La receta solo puede incluir insumos activos.']);
                        }
                        $syncData[$id] = ['quantity' => $qty];
                    }
                }
                $product->ingredients()->sync($syncData);
            });

        } catch (\Throwable $e) {
            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }
            throw $e;
        }
        if ($newImage && $oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('products.index', ['page' => $request->input('page', 1)])->with('success', 'Producto actualizado.');
    }

    public function destroy(Product $product)
    {
        // Eliminado lógico (desactivar) en lugar de borrar para mantener historial
        $product->update(['is_active' => false]);

        return redirect()->route('products.index')->with('success', 'Producto eliminado (desactivado).');
    }

    // Funciones extra para ajustes rápidos
    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => ! $product->is_active]);

        return back();
    }

    public function adjustStock(Request $request, Product $product)
    {
        $request->validate(['quantity' => 'required|numeric|min:0.001|max:99999999999', 'type' => 'required|in:add,sub']);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $product) {
            $product = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
            $oldStock = (float) $product->stock;
            $qty = round((float) $request->quantity, 3);
            if ($request->type === 'sub' && $qty > $oldStock) {
                throw \Illuminate\Validation\ValidationException::withMessages(['quantity' => 'No puede retirar más stock del disponible.']);
            }
            $delta = $request->type === 'sub' ? -$qty : $qty;
            $newStock = round($oldStock + $delta, 3);
            $product->update(['stock' => $newStock]);
            InventoryLog::create([
                'product_id' => $product->id, 'user_id' => Auth::id(),
                'type' => $delta < 0 ? 'adjustment_out' : 'adjustment_in',
                'quantity' => $delta, 'old_stock' => $oldStock, 'new_stock' => $newStock,
                'note' => 'Ajuste manual desde panel',
            ]);
        });

        return back()->with('success', 'Stock ajustado.');
    }
}
