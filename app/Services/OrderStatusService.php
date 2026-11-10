<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Exceptions\InsufficientStockOnConfirmException;
use App\Exceptions\InvalidStatusTransitionException;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class OrderStatusService
{
    /**
     * Konfirmasi pesanan oleh owner.
     * Mengubah status ke 'Diproses', mengunci baris varian, dan memotong stok (BR-03, BR-07).
     *
     * @throws InvalidStatusTransitionException
     * @throws InsufficientStockOnConfirmException
     */
    public function confirm(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $this->guardTransition($order, OrderStatus::Diproses);

            // Muat relasi items jika belum termuat
            $order->loadMissing('items');

            $variantIds = $order->items->pluck('product_variant_id')->filter()->all();

            $variants = ProductVariant::whereIn('id', $variantIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($order->items as $item) {
                if (! $item->product_variant_id) {
                    continue;
                }

                $variant = $variants->get($item->product_variant_id);

                if (! $variant) {
                    continue; // Varian mungkin sudah dihapus, riwayat tetap ada
                }

                if ($variant->stock_quantity < $item->quantity) {
                    throw new InsufficientStockOnConfirmException($order, $variant);
                }

                $variant->decrement('stock_quantity', $item->quantity);
            }

            $order->update([
                'status' => OrderStatus::Diproses,
                'confirmed_at' => now(),
            ]);

            $this->transition($order, OrderStatus::Diproses, 'Dikonfirmasi oleh owner');
        });
    }

    /**
     * Batalkan pesanan oleh owner beserta alasan pembatalan.
     * Jika pesanan berstatus 'Diproses' (sudah dikurangi stoknya), stok dikembalikan (BR-08).
     *
     * @throws InvalidStatusTransitionException
     */
    public function cancel(Order $order, string $reason): void
    {
        DB::transaction(function () use ($order, $reason) {
            $wasProcessed = $order->status === OrderStatus::Diproses;

            $this->guardTransition($order, OrderStatus::Dibatalkan);

            $order->loadMissing('items');

            // Kembalikan stok bila pesanan sebelumnya sudah dikonfirmasi/dipotong
            if ($wasProcessed) {
                foreach ($order->items as $item) {
                    if ($item->product_variant_id) {
                        ProductVariant::where('id', $item->product_variant_id)
                            ->increment('stock_quantity', $item->quantity);
                    }
                }
            }

            $order->update([
                'status' => OrderStatus::Dibatalkan,
                'cancellation_reason' => $reason,
            ]);

            $this->transition($order, OrderStatus::Dibatalkan, $reason);
        });
    }

    /**
     * Tandai pesanan telah dikirim beserta nomor resi pengiriman.
     *
     * @throws InvalidStatusTransitionException
     */
    public function markShipped(Order $order, ?string $trackingNumber = null): void
    {
        DB::transaction(function () use ($order, $trackingNumber) {
            $this->guardTransition($order, OrderStatus::Dikirim);

            $order->update([
                'status' => OrderStatus::Dikirim,
                'tracking_number' => $trackingNumber ?: $order->tracking_number,
            ]);

            $note = $trackingNumber ? "Nomor resi: {$trackingNumber}" : 'Pesanan telah dikirim ke ekspedisi';
            $this->transition($order, OrderStatus::Dikirim, $note);
        });
    }

    /**
     * Tandai pesanan selesai diterima oleh pembeli.
     *
     * @throws InvalidStatusTransitionException
     */
    public function markCompleted(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $this->guardTransition($order, OrderStatus::Selesai);

            $order->update([
                'status' => OrderStatus::Selesai,
            ]);

            $this->transition($order, OrderStatus::Selesai, 'Pesanan selesai');
        });
    }

    /**
     * Memastikan transisi status mematuhi state machine BR-08.
     *
     * @throws InvalidStatusTransitionException
     */
    private function guardTransition(Order $order, OrderStatus $target): void
    {
        if (! $order->status->canTransitionTo($target)) {
            throw new InvalidStatusTransitionException($order->status, $target);
        }
    }

    /**
     * Catat log riwayat status ke tabel order_status_logs.
     */
    private function transition(Order $order, OrderStatus $to, ?string $note = null): void
    {
        $from = $order->getOriginal('status') ?? $order->status;
        $fromValue = $from instanceof OrderStatus ? $from->value : (string) $from;

        $order->statusLogs()->create([
            'from_status' => $fromValue,
            'to_status'   => $to->value,
            'note'        => $note,
            'created_at'  => now(),
        ]);
    }
}
