<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\ShippingZone;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingZoneManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private ShippingZone $zone;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create(['is_owner' => true]);

        $this->zone = ShippingZone::create([
            'name' => 'Zona 1 - Surabaya Raya',
            'base_rate' => 10000,
            'rate_per_extra_kg' => 5000,
            'description' => 'Wilayah Surabaya & sekitarnya',
            'sort_order' => 1,
            'is_active' => true,
        ]);
    }

    public function test_owner_can_view_shipping_zones_index(): void
    {
        $response = $this->actingAs($this->owner)->get('/admin/ongkir');

        $response->assertStatus(200);
        $response->assertSee('Tarif Ongkir');
        $response->assertSee('Zona 1 - Surabaya Raya');
        $response->assertSee('10.000');
    }

    public function test_owner_can_create_new_shipping_zone(): void
    {
        $response = $this->actingAs($this->owner)->post('/admin/ongkir', [
            'name' => 'Zona Luar Jawa',
            'base_rate' => 45000,
            'rate_per_extra_kg' => 20000,
            'description' => 'Sumatra, Kalimantan, Sulawesi',
            'sort_order' => 2,
            'is_active' => 1,
        ]);

        $response->assertRedirect('/admin/ongkir');
        $this->assertDatabaseHas('shipping_zones', [
            'name' => 'Zona Luar Jawa',
            'base_rate' => 45000,
        ]);
    }

    public function test_owner_can_update_shipping_zone(): void
    {
        $response = $this->actingAs($this->owner)->put("/admin/ongkir/{$this->zone->id}", [
            'name' => 'Zona 1 - Surabaya & Sidoarjo',
            'base_rate' => 12000,
            'rate_per_extra_kg' => 6000,
            'description' => 'Wilayah Surabaya dan Sidoarjo',
            'sort_order' => 1,
            'is_active' => 1,
        ]);

        $response->assertRedirect('/admin/ongkir');
        $this->zone->refresh();
        $this->assertEquals('Zona 1 - Surabaya & Sidoarjo', $this->zone->name);
        $this->assertEquals(12000, $this->zone->base_rate);
    }

    public function test_owner_can_toggle_shipping_zone_active_status(): void
    {
        $this->assertTrue($this->zone->is_active);

        $response = $this->actingAs($this->owner)->patch("/admin/ongkir/{$this->zone->id}/toggle-active");

        $response->assertRedirect();
        $this->zone->refresh();
        $this->assertFalse($this->zone->is_active);
    }

    public function test_shipping_zone_with_orders_cannot_be_deleted(): void
    {
        Order::create([
            'order_number' => 'ORD-999',
            'customer_name' => 'Test Customer',
            'customer_phone' => '081234567890',
            'address_line' => 'Jl. Test',
            'city' => 'Surabaya',
            'province' => 'Jatim',
            'postal_code' => '60000',
            'shipping_zone_id' => $this->zone->id,
            'shipping_zone_name' => $this->zone->name,
            'total_weight_gram' => 100,
            'subtotal' => 50000,
            'shipping_cost' => 10000,
            'total' => 60000,
            'status' => 'menunggu_konfirmasi',
        ]);

        $response = $this->actingAs($this->owner)->delete("/admin/ongkir/{$this->zone->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('shipping_zones', ['id' => $this->zone->id]);
    }

    public function test_shipping_zone_without_orders_can_be_deleted(): void
    {
        $newZone = ShippingZone::create([
            'name' => 'Zona Kosong',
            'base_rate' => 20000,
            'rate_per_extra_kg' => 10000,
            'sort_order' => 99,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->owner)->delete("/admin/ongkir/{$newZone->id}");

        $response->assertRedirect('/admin/ongkir');
        $this->assertDatabaseMissing('shipping_zones', ['id' => $newZone->id]);
    }
}
