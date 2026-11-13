<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = $this->faker->numberBetween(50000, 300000);
        $shipping = 15000;

        return [
            'user_id' => null,
            'order_number' => 'RHS-' . strtoupper($this->faker->bothify('####??')),
            'customer_name' => $this->faker->name(),
            'customer_phone' => '0812' . $this->faker->numerify('########'),
            'address_line' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'province' => 'Jawa Timur',
            'postal_code' => $this->faker->postcode(),
            'note' => $this->faker->optional()->sentence(),
            'shipping_zone_id' => null,
            'shipping_zone_name' => 'Zona 1 - Jawa Timur',
            'total_weight_gram' => $this->faker->numberBetween(200, 1000),
            'subtotal' => $subtotal,
            'shipping_cost' => $shipping,
            'total' => $subtotal + $shipping,
            'status' => OrderStatus::MenungguKonfirmasi,
            'cancellation_reason' => null,
            'tracking_number' => null,
            'payload_json' => null,
            'sent_to_wa_at' => null,
            'confirmed_at' => null,
        ];
    }
}
