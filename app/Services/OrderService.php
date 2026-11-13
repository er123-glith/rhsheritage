<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Exceptions\EmptyCartException;
use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\ShippingZone;
use Illuminate\Support\Facades\DB;

/**
 * Data pesanan dari form checkout.
 * Selalu gunakan fromRequest() untuk membangun dari PlaceOrderRequest.
 */
class PlaceOrderData
{
    public function __construct(
        public readonly string $customerName,
        public readonly string $customerPhone,
        public readonly string $addressLine,
        public readonly string $addressCity,
        public readonly string $addressProvince,
        public readonly string $addressPostalCode,
        public readonly ?string $addressNote,
        public readonly int $shippingZoneId,
        /** @var array<int, int> variant_id => quantity */
        public readonly array $items,
    ) {}

    public static function fromRequest(\App\Http\Requests\PlaceOrderRequest $request, array $cartItems): static
    {
        return new static(
            customerName:       $request->validated('customer_name'),
            customerPhone:      $request->validated('customer_phone'),
            addressLine:        $request->validated('address_line'),
            addressCity:        $request->validated('address_city'),
            addressProvince:    $request->validated('address_province'),
            addressPostalCode:  $request->validated('address_postal_code'),
            addressNote:        $request->validated('address_note'),
            shippingZoneId:     (int) $request->validated('shipping_zone_id'),
            items:              $cartItems,
        );
    }
}

class OrderService
{
    public function __construct(
        private readonly OrderNumberGenerator $numberGenerator,
        private readonly ShippingCalculator $shippingCalculator,
        private readonly WhatsAppMessageBuilder $messageBuilder,
    ) {}

    /**
     * Buat pesanan baru dari data checkout.
     *
     * Alur (sesuai architecture.md Section 10.2):
     * 1. Validasi keranjang tidak kosong
     * 2. Buka transaksi DB
     * 3. Lock semua varian (lockForUpdate)
     * 4. Validasi stok setiap item
     * 5. Hitung ulang harga dan ongkir di server (TIDAK dari input klien)
     * 6. Generate nomor pesanan unik
     * 7. Simpan orders + order_items (snapshot) + order_status_logs
     * 8. Simpan payload_json dan marked sent_to_wa_at = null (belum dikirim)
     * 9. TIDAK mengurangi stok (stok dikurangi saat owner konfirmasi — BR-03)
     * 10. Commit transaksi
     *
     * @throws EmptyCartException
     * @throws InsufficientStockException
     */
    public function place(PlaceOrderData $data): Order
    {
        if (empty($data->items)) {
            throw new EmptyCartException();
        }

        return DB::transaction(function () use ($data) {
            $zone = ShippingZone::findOrFail($data->shippingZoneId);

            // Lock semua varian yang akan dipesan untuk mencegah race condition
            $variantIds = array_keys($data->items);
            $variants = ProductVariant::with('product')
                ->whereIn('id', $variantIds)
                ->where('is_active', true)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // Validasi ketersediaan setiap varian
            foreach ($data->items as $variantId => $qty) {
                $variant = $variants->get($variantId);
                if (! $variant) {
                    throw new \RuntimeException("Varian produk tidak ditemukan atau tidak aktif (ID: {$variantId}).");
                }
                if ($variant->stock_quantity < $qty) {
                    throw new InsufficientStockException($variant, $variant->stock_quantity, $qty);
                }
            }

            // Hitung ulang subtotal di server (TIDAK pakai angka dari klien)
            $subtotal = 0;
            $totalWeightGram = 0;
            $orderItemsData = [];

            foreach ($data->items as $variantId => $qty) {
                $variant = $variants[$variantId];
                $unitPrice = (int) $variant->price;
                $itemSubtotal = $unitPrice * $qty;
                $subtotal += $itemSubtotal;
                $totalWeightGram += ($variant->product->weight_gram ?? 0) * $qty;

                $orderItemsData[] = [
                    'product_variant_id' => $variantId,
                    'product_name'       => $variant->product->name,   // snapshot
                    'variant_label'      => $variant->variant_label,    // snapshot
                    'unit_price'         => $unitPrice,                  // snapshot
                    'quantity'           => $qty,
                    'subtotal'           => $itemSubtotal,
                ];
            }

            // Hitung ongkir di server
            $shippingCost = $this->shippingCalculator->calculateFromGram($totalWeightGram, $zone);
            $total = $subtotal + $shippingCost;

            // Generate nomor pesanan unik
            $orderNumber = $this->numberGenerator->generate();

            // Buat pesanan
            $order = Order::create([
                'user_id'             => auth()->id(),
                'order_number'        => $orderNumber,
                'customer_name'       => $data->customerName,
                'customer_phone'      => $data->customerPhone,
                'address_line'        => $data->addressLine,
                'city'                => $data->addressCity,
                'province'            => $data->addressProvince,
                'postal_code'         => $data->addressPostalCode,
                'note'                => $data->addressNote,
                'shipping_zone_id'    => $data->shippingZoneId,
                'shipping_zone_name'  => $zone->name,
                'total_weight_gram'   => $totalWeightGram,
                'subtotal'            => $subtotal,
                'shipping_cost'       => $shippingCost,
                'total'               => $total,
                'status'              => OrderStatus::MenungguKonfirmasi,
                'payload_json'        => null, // diisi setelah order_items tersimpan
                'sent_to_wa_at'       => null, // belum dikirim ke WA
            ]);

            // Simpan item pesanan dengan snapshot harga
            foreach ($orderItemsData as $itemData) {
                $order->orderItems()->create($itemData);
            }

            // Catat log status awal
            $order->statusLogs()->create([
                'from_status' => null,
                'to_status'   => OrderStatus::MenungguKonfirmasi->value,
                'note'        => 'Pesanan diterima dari sistem.',
            ]);

            // Reload relasi untuk membangun payload JSON
            $order->load(['orderItems', 'shippingZone']);

            // Simpan payload JSON ke order (BR-06a: pesanan tersimpan sebelum WA dibuka)
            $payload = $this->messageBuilder->buildPayload($order);
            $order->update(['payload_json' => $payload]);

            return $order->fresh(['orderItems', 'shippingZone', 'statusLogs']);
        });
    }
}
