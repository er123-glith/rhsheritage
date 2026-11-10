<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVariantRequest;
use App\Http\Requests\Admin\UpdateVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VariantController extends Controller
{
    public function store(StoreVariantRequest $request, Product $product): RedirectResponse
    {
        $names = $request->input('attribute_name');
        $values = $request->input('attribute_value');

        $attributeMap = [];
        foreach ($names as $i => $name) {
            $attributeMap[trim($name)] = trim($values[$i]);
        }

        $variantLabel = collect($attributeMap)
            ->map(fn ($value, $name) => "{$name} {$value}")
            ->implode(' · ');

        if ($product->variants()->where('variant_label', $variantLabel)->exists()) {
            throw ValidationException::withMessages([
                'attribute_value' => 'Kombinasi varian ini sudah ada untuk produk ini.',
            ]);
        }

        DB::transaction(function () use ($product, $names, $values, $attributeMap, $variantLabel, $request) {
            foreach ($names as $i => $name) {
                $attribute = $product->variantAttributes()->firstOrCreate(
                    ['name' => trim($name)],
                    ['sort_order' => $product->variantAttributes()->count()]
                );

                $attribute->values()->firstOrCreate(
                    ['value' => trim($values[$i])],
                    ['sort_order' => $attribute->values()->count()]
                );
            }

            $product->variants()->create([
                'sku' => $request->input('sku') ?: null,
                'variant_label' => $variantLabel,
                'attribute_map' => $attributeMap,
                'price' => $request->input('price'),
                'stock_quantity' => $request->input('stock_quantity'),
                'is_active' => $request->boolean('is_active', true),
            ]);
        });

        return back()->with('status', 'Varian produk berhasil ditambahkan.');
    }

    public function update(UpdateVariantRequest $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        abort_unless($variant->product_id === $product->id, 404);

        $variant->update([
            'sku' => $request->input('sku') ?: null,
            'price' => $request->input('price'),
            'stock_quantity' => $request->input('stock_quantity'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', 'Varian produk berhasil diperbarui.');
    }

    public function destroy(Product $product, ProductVariant $variant): RedirectResponse
    {
        abort_unless($variant->product_id === $product->id, 404);

        if ($product->variants()->count() <= 1) {
            return back()->with('error', 'Produk wajib memiliki minimal satu varian.');
        }

        $variant->delete();

        return back()->with('status', 'Varian produk berhasil dihapus.');
    }
}
