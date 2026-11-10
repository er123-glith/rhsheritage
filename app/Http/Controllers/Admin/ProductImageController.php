<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductImageRequest;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImageController extends Controller
{
    public function store(StoreProductImageRequest $request, Product $product): RedirectResponse
    {
        $startingOrder = $product->images()->count();

        $manager = new ImageManager(new Driver());

        foreach ($request->file('images') as $index => $file) {
            $filename = Str::uuid().'.webp';
            $relativePath = "products/{$product->id}/{$filename}";

            try {
                // Intervention Image v4 API
                $image = $manager->decode($file->getRealPath());
                $image->scaleDown(width: 1200);
                $encoded = $image->encodeUsingFormat(Format::WEBP);

                Storage::disk('public')->put($relativePath, (string) $encoded);
            } catch (\Throwable $e) {
                // Fallback: store file directly if conversion is not supported by GD driver
                $ext = $file->getClientOriginalExtension() ?: 'jpg';
                $filename = Str::uuid().'.'.$ext;
                $relativePath = "products/{$product->id}/{$filename}";
                Storage::disk('public')->putFileAs("products/{$product->id}", $file, $filename);
            }

            $product->images()->create([
                'path' => $relativePath,
                'alt_text' => $request->input('alt_text') ?: $product->name,
                'sort_order' => $startingOrder + $index,
            ]);
        }

        return back()->with('status', 'Foto produk berhasil diunggah.');
    }

    public function destroy(Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('status', 'Foto produk berhasil dihapus.');
    }
}
