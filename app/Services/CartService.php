<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\ProductVariant;
use App\Models\ShippingZone;
use App\Support\Cart\CartItem;
use App\Support\Cart\CartSummary;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartService
{
    const KEY = 'cart';

    /**
     * Tambah atau increment varian ke keranjang.
     *
     * @throws InsufficientStockException
     */
    public function add(int $variantId, int $qty = 1): void
    {
        $variant = ProductVariant::with('product')->findOrFail($variantId);

        $currentQty = $this->rawItems()[$variantId] ?? 0;
        $newQty = $currentQty + $qty;

        if ($variant->stock_quantity < $newQty) {
            throw new InsufficientStockException($variant, $variant->stock_quantity, $newQty);
        }

        $raw = $this->rawItems();
        $raw[$variantId] = $newQty;
        Session::put(self::KEY, $raw);
    }

    /**
     * Set kuantitas varian ke nilai tertentu.
     *
     * @throws InsufficientStockException
     */
    public function update(int $variantId, int $qty): void
    {
        if ($qty <= 0) {
            $this->remove($variantId);
            return;
        }

        $variant = ProductVariant::with('product')->findOrFail($variantId);

        if ($variant->stock_quantity < $qty) {
            throw new InsufficientStockException($variant, $variant->stock_quantity, $qty);
        }

        $raw = $this->rawItems();
        $raw[$variantId] = $qty;
        Session::put(self::KEY, $raw);
    }

    /** Hapus varian dari keranjang. */
    public function remove(int $variantId): void
    {
        $raw = $this->rawItems();
        unset($raw[$variantId]);
        Session::put(self::KEY, $raw);
    }

    /** Kosongkan seluruh keranjang. */
    public function clear(): void
    {
        Session::forget(self::KEY);
    }

    /** Jumlah item unik dalam keranjang. */
    public function count(): int
    {
        return count($this->rawItems());
    }

    /** Total kuantitas (jumlah unit) dalam keranjang. */
    public function totalQty(): int
    {
        return array_sum($this->rawItems());
    }

    /**
     * Ambil koleksi CartItem.
     * Varian yang tidak aktif atau dihapus akan difilter otomatis.
     *
     * @return Collection<CartItem>
     */
    public function items(): Collection
    {
        $raw = $this->rawItems();
        if (empty($raw)) {
            return collect();
        }

        $variants = ProductVariant::with(['product.category', 'product.images'])
            ->whereIn('id', array_keys($raw))
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        return collect($raw)
            ->filter(fn($qty, $variantId) => $variants->has($variantId))
            ->map(fn($qty, $variantId) => CartItem::fromVariant($variants[$variantId], $qty))
            ->values();
    }

    /**
     * Buat ringkasan keranjang lengkap dengan perhitungan ongkir.
     */
    public function summary(?ShippingZone $zone = null): CartSummary
    {
        $items = $this->items();
        $subtotal = $items->sum(fn(CartItem $item) => $item->subtotal());

        $shippingCost = 0;
        if ($zone && $items->isNotEmpty()) {
            $shippingCost = app(ShippingCalculator::class)->calculate($items, $zone);
        }

        return new CartSummary(
            items: $items,
            subtotal: $subtotal,
            shippingCost: $shippingCost,
            total: $subtotal + $shippingCost,
            zone: $zone,
        );
    }

    /**
     * Validasi seluruh item keranjang terhadap ketersediaan stok terkini.
     * Mengembalikan daftar CartItem yang bermasalah (stok tidak mencukupi).
     *
     * @return Collection<CartItem>
     */
    public function validateAvailability(): Collection
    {
        return $this->items()->filter(
            fn(CartItem $item) => $item->variant->stock_quantity < $item->quantity
        );
    }

    /** Array mentah dari sesi: [variantId => qty] */
    private function rawItems(): array
    {
        return Session::get(self::KEY, []);
    }
}
