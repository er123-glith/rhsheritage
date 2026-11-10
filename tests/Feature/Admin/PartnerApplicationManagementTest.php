<?php

namespace Tests\Feature\Admin;

use App\Enums\ApplicationType;
use App\Models\PartnerApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerApplicationManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private PartnerApplication $application;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create(['is_owner' => true]);

        $this->application = PartnerApplication::create([
            'type' => ApplicationType::Grosir,
            'applicant_name' => 'Ustadz Mansur',
            'institution_name' => 'Pesantren Darul Ulum',
            'phone' => '081234567899',
            'email' => 'pesantren@example.com',
            'region' => 'Jombang',
            'product_interest' => 'Songkok AC',
            'estimated_quantity' => '50 kodi',
            'message' => 'Mohon info harga dan katalog lengkap.',
            'is_followed_up' => false,
        ]);
    }

    public function test_owner_can_view_partner_applications_index(): void
    {
        $response = $this->actingAs($this->owner)->get('/admin/grosir-mitra');

        $response->assertStatus(200);
        $response->assertSee('Pengajuan Grosir');
        $response->assertSee('Ustadz Mansur');
        $response->assertSee('Pesantren Darul Ulum');
    }

    public function test_owner_can_toggle_follow_up_status(): void
    {
        $this->assertFalse($this->application->is_followed_up);

        $response = $this->actingAs($this->owner)->patch("/admin/grosir-mitra/{$this->application->id}/toggle-followup");

        $response->assertRedirect();
        $this->application->refresh();
        $this->assertTrue($this->application->is_followed_up);

        // Toggle kembali
        $this->actingAs($this->owner)->patch("/admin/grosir-mitra/{$this->application->id}/toggle-followup");
        $this->application->refresh();
        $this->assertFalse($this->application->is_followed_up);
    }

    public function test_owner_can_delete_application(): void
    {
        $response = $this->actingAs($this->owner)->delete("/admin/grosir-mitra/{$this->application->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('partner_applications', [
            'id' => $this->application->id,
        ]);
    }
}
