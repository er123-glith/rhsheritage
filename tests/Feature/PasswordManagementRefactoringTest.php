<?php

namespace Tests\Feature;

use App\Models\AdminActivityLog;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class PasswordManagementRefactoringTest extends TestCase
{
    use RefreshDatabase;

    private function createCustomer(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role'     => 'customer',
            'is_owner' => false,
            'password' => Hash::make('OldPassword12345'),
        ], $attributes));
    }

    private function createAdmin(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role'     => 'admin',
            'is_owner' => true,
            'password' => Hash::make('AdminSecret12345'),
        ], $attributes));
    }

    /**
     * 1. Customer dapat mengganti password sendiri via Profil.
     */
    public function test_01_customer_can_update_own_password(): void
    {
        $customer = $this->createCustomer();

        $response = $this->actingAs($customer)
            ->put('/password', [
                'current_password'      => 'OldPassword12345',
                'password'              => 'NewCustomerPassword123!',
                'password_confirmation' => 'NewCustomerPassword123!',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $response->assertSessionHas('status', 'password-updated');

        $this->assertTrue(Hash::check('NewCustomerPassword123!', $customer->fresh()->password));
    }

    /**
     * 2. Customer wajib memasukkan password saat ini.
     */
    public function test_02_customer_must_provide_current_password(): void
    {
        $customer = $this->createCustomer();

        $response = $this->actingAs($customer)
            ->put('/password', [
                'current_password'      => '',
                'password'              => 'NewCustomerPassword123!',
                'password_confirmation' => 'NewCustomerPassword123!',
            ]);

        $response->assertSessionHasErrorsIn('updatePassword', 'current_password');
    }

    /**
     * 3. Password saat ini yang salah ditolak.
     */
    public function test_03_customer_wrong_current_password_is_rejected(): void
    {
        $customer = $this->createCustomer();

        $response = $this->actingAs($customer)
            ->put('/password', [
                'current_password'      => 'TotallyWrongPassword999',
                'password'              => 'NewCustomerPassword123!',
                'password_confirmation' => 'NewCustomerPassword123!',
            ]);

        $response->assertSessionHasErrorsIn('updatePassword', 'current_password');
        $this->assertTrue(Hash::check('OldPassword12345', $customer->fresh()->password));
    }

    /**
     * 4. Password baru terlalu pendek (< 12 karakter) ditolak.
     */
    public function test_04_customer_new_password_too_short_is_rejected(): void
    {
        $customer = $this->createCustomer();

        // 11 karakter (kurang dari syarat minimal 12 karakter)
        $response = $this->actingAs($customer)
            ->put('/password', [
                'current_password'      => 'OldPassword12345',
                'password'              => 'Short12345!',
                'password_confirmation' => 'Short12345!',
            ]);

        $response->assertSessionHasErrorsIn('updatePassword', 'password');
    }

    /**
     * 5. Konfirmasi password tidak cocok ditolak.
     */
    public function test_05_customer_password_confirmation_mismatch_is_rejected(): void
    {
        $customer = $this->createCustomer();

        $response = $this->actingAs($customer)
            ->put('/password', [
                'current_password'      => 'OldPassword12345',
                'password'              => 'NewCustomerPassword123!',
                'password_confirmation' => 'MismatchConfirmation999!',
            ]);

        $response->assertSessionHasErrorsIn('updatePassword', 'password');
    }

    /**
     * 6. Password baru sama dengan password lama ditolak.
     */
    public function test_06_customer_new_password_same_as_current_is_rejected(): void
    {
        $customer = $this->createCustomer();

        $response = $this->actingAs($customer)
            ->put('/password', [
                'current_password'      => 'OldPassword12345',
                'password'              => 'OldPassword12345',
                'password_confirmation' => 'OldPassword12345',
            ]);

        $response->assertSessionHasErrorsIn('updatePassword', 'password');
    }

    /**
     * 7. Customer tidak dapat mengganti password akun lain (hanya auth()->user() yang diupdate).
     */
    public function test_07_customer_cannot_update_other_user_password_via_request(): void
    {
        $customerA = $this->createCustomer(['email' => 'customera@rhs.id']);
        $customerB = $this->createCustomer(['email' => 'customerb@rhs.id', 'password' => Hash::make('VictimPass12345')]);

        // Customer A mengirim manipulasi user_id target B
        $response = $this->actingAs($customerA)
            ->put('/password', [
                'user_id'               => $customerB->id,
                'current_password'      => 'OldPassword12345',
                'password'              => 'HackedPassword123!',
                'password_confirmation' => 'HackedPassword123!',
            ]);

        $response->assertSessionHasNoErrors();
        // Akun B tetap memiliki password lamanya
        $this->assertTrue(Hash::check('VictimPass12345', $customerB->fresh()->password));
        // Yang berubah adalah password Customer A
        $this->assertTrue(Hash::check('HackedPassword123!', $customerA->fresh()->password));
    }

    /**
     * 8. Guest tidak dapat mengakses endpoint perubahan password.
     */
    public function test_08_guest_cannot_access_password_update_endpoint(): void
    {
        $response = $this->put('/password', [
            'current_password'      => 'Whatever12345',
            'password'              => 'NewPassword12345!',
            'password_confirmation' => 'NewPassword12345!',
        ]);

        $response->assertRedirect('/login');
    }

    /**
     * 9. Admin dapat mengganti password customer melalui modul manajemen customer.
     */
    public function test_09_admin_can_update_customer_password(): void
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();

        // Akses form ganti password
        $formResponse = $this->actingAs($admin)->get("/admin/pelanggan/{$customer->id}/ganti-password");
        $formResponse->assertStatus(200);
        $formResponse->assertSee($customer->name);
        $formResponse->assertSee($customer->email);

        // Submit form ganti password
        $updateResponse = $this->actingAs($admin)
            ->put("/admin/pelanggan/{$customer->id}/ganti-password", [
                'password'              => 'AdminAssignedPass123!',
                'password_confirmation' => 'AdminAssignedPass123!',
                'admin_password'        => 'AdminSecret12345',
            ]);

        $updateResponse->assertSessionHasNoErrors();
        $updateResponse->assertRedirect(route('admin.customers.show', $customer));

        $this->assertTrue(Hash::check('AdminAssignedPass123!', $customer->fresh()->password));
    }

    /**
     * 10. Admin wajib memverifikasi password sendiri saat mengganti password customer.
     */
    public function test_10_admin_must_verify_own_password(): void
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();

        $response = $this->actingAs($admin)
            ->put("/admin/pelanggan/{$customer->id}/ganti-password", [
                'password'              => 'AdminAssignedPass123!',
                'password_confirmation' => 'AdminAssignedPass123!',
                'admin_password'        => '',
            ]);

        $response->assertSessionHasErrors('admin_password');
        $this->assertTrue(Hash::check('OldPassword12345', $customer->fresh()->password));
    }

    /**
     * 11. Password admin yang salah menyebabkan request ditolak.
     */
    public function test_11_admin_wrong_password_is_rejected(): void
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();

        $response = $this->actingAs($admin)
            ->put("/admin/pelanggan/{$customer->id}/ganti-password", [
                'password'              => 'AdminAssignedPass123!',
                'password_confirmation' => 'AdminAssignedPass123!',
                'admin_password'        => 'WrongAdminPassword999',
            ]);

        $response->assertSessionHasErrors('admin_password');
        $this->assertTrue(Hash::check('OldPassword12345', $customer->fresh()->password));
    }

    /**
     * 12. Admin tidak dapat mengubah password akun admin lain melalui modul customer.
     */
    public function test_12_admin_cannot_change_other_admin_password_via_customer_module(): void
    {
        $admin = $this->createAdmin(['email' => 'admin1@rhs.id']);
        $otherAdmin = $this->createAdmin(['email' => 'admin2@rhs.id', 'password' => Hash::make('Admin2Secret12345')]);

        // GET harus ditolak 403
        $getResponse = $this->actingAs($admin)->get("/admin/pelanggan/{$otherAdmin->id}/ganti-password");
        $getResponse->assertStatus(403);

        // PUT harus ditolak 403
        $putResponse = $this->actingAs($admin)
            ->put("/admin/pelanggan/{$otherAdmin->id}/ganti-password", [
                'password'              => 'NewAdminPassword123!',
                'password_confirmation' => 'NewAdminPassword123!',
                'admin_password'        => 'AdminSecret12345',
            ]);
        $putResponse->assertStatus(403);

        $this->assertTrue(Hash::check('Admin2Secret12345', $otherAdmin->fresh()->password));
    }

    /**
     * 13. Customer tidak dapat mengakses endpoint ganti password admin.
     */
    public function test_13_customer_cannot_access_admin_password_endpoints(): void
    {
        $customer = $this->createCustomer();
        $targetCustomer = $this->createCustomer(['email' => 'target@rhs.id']);

        $getResponse = $this->actingAs($customer)->get("/admin/pelanggan/{$targetCustomer->id}/ganti-password");
        $getResponse->assertStatus(403);

        $putResponse = $this->actingAs($customer)->put("/admin/pelanggan/{$targetCustomer->id}/ganti-password", [
            'password'              => 'AttackerPassword123!',
            'password_confirmation' => 'AttackerPassword123!',
            'admin_password'        => 'OldPassword12345',
        ]);
        $putResponse->assertStatus(403);
    }

    /**
     * 14. Password baru tersimpan sebagai hash bcrypt / argon aman (bukan plaintext).
     */
    public function test_14_new_password_is_stored_as_secure_hash(): void
    {
        $customer = $this->createCustomer();

        $this->actingAs($customer)->put('/password', [
            'current_password'      => 'OldPassword12345',
            'password'              => 'StrongHashedPassword123!',
            'password_confirmation' => 'StrongHashedPassword123!',
        ]);

        $freshPassword = $customer->fresh()->password;

        // Pastikan tidak ada karakter plaintext langsung
        $this->assertNotEquals('StrongHashedPassword123!', $freshPassword);
        // Pastikan merupakan hash valid Laravel
        $this->assertNotEmpty(Hash::info($freshPassword)['algo']);
    }

    /**
     * 15. Password lama tidak dapat digunakan untuk login setelah perubahan.
     */
    public function test_15_old_password_cannot_be_used_to_login_after_change(): void
    {
        $customer = $this->createCustomer();

        $this->actingAs($customer)->put('/password', [
            'current_password'      => 'OldPassword12345',
            'password'              => 'ChangedValidPassword123!',
            'password_confirmation' => 'ChangedValidPassword123!',
        ]);

        Auth::logout();

        $this->assertFalse(Auth::validate([
            'email'    => $customer->email,
            'password' => 'OldPassword12345',
        ]));
    }

    /**
     * 16. Password baru dapat digunakan untuk login.
     */
    public function test_16_new_password_can_be_used_to_login(): void
    {
        $customer = $this->createCustomer();

        $this->actingAs($customer)->put('/password', [
            'current_password'      => 'OldPassword12345',
            'password'              => 'BrandNewPassword123!',
            'password_confirmation' => 'BrandNewPassword123!',
        ]);

        Auth::logout();

        $this->assertTrue(Auth::validate([
            'email'    => $customer->email,
            'password' => 'BrandNewPassword123!',
        ]));

        $loginResponse = $this->post('/login', [
            'email'    => $customer->email,
            'password' => 'BrandNewPassword123!',
        ]);

        $loginResponse->assertRedirect();
        $this->assertAuthenticatedAs($customer);
    }

    /**
     * 17. Perubahan password customer oleh admin dicatat dalam audit log.
     */
    public function test_17_admin_password_change_is_recorded_in_audit_log(): void
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();

        $this->actingAs($admin)->put("/admin/pelanggan/{$customer->id}/ganti-password", [
            'password'              => 'AuditedNewPassword123!',
            'password_confirmation' => 'AuditedNewPassword123!',
            'admin_password'        => 'AdminSecret12345',
        ]);

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id'       => $admin->id,
            'target_user_id' => $customer->id,
            'action'         => 'password_changed_by_admin',
            'status'         => 'success',
        ]);
    }

    /**
     * 18. Audit log dan tabel sistem tidak menyimpan password atau token dalam plaintext.
     */
    public function test_18_audit_log_does_not_contain_plaintext_password_hash_or_token(): void
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();

        $rawPassword = 'SuperSecretPlainPass123!';
        $this->actingAs($admin)->put("/admin/pelanggan/{$customer->id}/ganti-password", [
            'password'              => $rawPassword,
            'password_confirmation' => $rawPassword,
            'admin_password'        => 'AdminSecret12345',
        ]);

        $log = AdminActivityLog::where('target_user_id', $customer->id)->latest('id')->first();
        $this->assertNotNull($log);

        $logString = json_encode($log->toArray());

        // Pastikan tidak ada kata sandi mentah di log
        $this->assertStringNotContainsString($rawPassword, $logString);
        $this->assertStringNotContainsString('AdminSecret12345', $logString);
        // Pastikan tidak ada hash kata sandi di log
        $this->assertStringNotContainsString('$2y$', $logString);
    }

    /**
     * 19. Akun customer nonaktif (soft-deleted) tidak dapat mengubah password.
     */
    public function test_19_inactive_soft_deleted_customer_cannot_update_password(): void
    {
        $customer = $this->createCustomer();
        $customer->delete(); // Soft delete

        // Melalui self-service
        $response = $this->actingAs($customer)->put('/password', [
            'current_password'      => 'OldPassword12345',
            'password'              => 'TryNewPassword123!',
            'password_confirmation' => 'TryNewPassword123!',
        ]);

        // Harus dicegah (403 atau redirect login oleh EnsureAccountIsActive)
        $this->assertTrue(in_array($response->status(), [302, 403]));

        // Melalui modul admin
        $admin = $this->createAdmin();
        $adminResponse = $this->actingAs($admin)->put("/admin/pelanggan/{$customer->id}/ganti-password", [
            'password'              => 'AdminAssignPass123!',
            'password_confirmation' => 'AdminAssignPass123!',
            'admin_password'        => 'AdminSecret12345',
        ]);

        $adminResponse->assertSessionHas('error');
    }

    /**
     * 20. Sesi customer yang dicabut dibersihkan dari tabel sessions dan remember_token diputar.
     */
    public function test_20_revoked_customer_sessions_are_removed_from_sessions_table(): void
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer(['remember_token' => 'original-token-12345']);

        // Masukkan sesi aktif pelanggan ke database tabel sessions
        DB::table('sessions')->insert([
            'id'            => 'customer-session-token-abc',
            'user_id'       => $customer->id,
            'ip_address'    => '127.0.0.1',
            'user_agent'    => 'PHPUnit Simulator',
            'payload'       => base64_encode(serialize(['user_id' => $customer->id])),
            'last_activity' => time(),
        ]);

        $this->assertDatabaseHas('sessions', ['id' => 'customer-session-token-abc']);

        // Admin mereset password customer
        $this->actingAs($admin)->put("/admin/pelanggan/{$customer->id}/ganti-password", [
            'password'              => 'RotatedSessionPass123!',
            'password_confirmation' => 'RotatedSessionPass123!',
            'admin_password'        => 'AdminSecret12345',
        ]);

        // Sesi di tabel sessions harus sudah dihapus
        $this->assertDatabaseMissing('sessions', ['id' => 'customer-session-token-abc']);
        // remember_token harus sudah diputar
        $this->assertNotEquals('original-token-12345', $customer->fresh()->remember_token);
    }

    /**
     * 21. Admin Seeder tidak menimpa password admin yang telah diganti.
     */
    public function test_21_admin_user_seeder_does_not_overwrite_changed_admin_password(): void
    {
        config(['shop.admin_email' => 'admin@rhsheritage.id']);
        config(['shop.admin_password' => 'SeederDefaultPassword123!']);

        // Admin telah mengganti passwordnya sendiri
        $admin = User::create([
            'name'     => 'RHS Heritage Admin',
            'email'    => 'admin@rhsheritage.id',
            'password' => Hash::make('CustomChangedAdminPass123!'),
            'role'     => 'admin',
            'is_owner' => true,
        ]);

        // Jalankan seeder
        $seeder = new AdminUserSeeder();
        $seeder->run();

        // Password tetap merupakan kata sandi kustom yang diganti, bukan seeder default
        $this->assertTrue(Hash::check('CustomChangedAdminPass123!', $admin->fresh()->password));
        $this->assertFalse(Hash::check('SeederDefaultPassword123!', $admin->fresh()->password));
    }

    /**
     * 22. Route forgot-password dan reset-password lama tidak dapat digunakan (404).
     */
    public function test_22_forgot_password_and_reset_password_routes_return_404(): void
    {
        $this->get('/forgot-password')->assertStatus(404);
        $this->post('/forgot-password', ['email' => 'admin@rhs.id'])->assertStatus(404);
        $this->get('/reset-password/sample-token')->assertStatus(404);
        $this->post('/reset-password', ['token' => 'sample-token'])->assertStatus(404);
    }

    /**
     * 23. Login, registrasi, checkout, dan dashboard tetap berfungsi normal.
     */
    public function test_23_login_registration_checkout_and_dashboards_remain_functional(): void
    {
        // 1. Registrasi customer baru
        $regResponse = $this->post('/register', [
            'name'                  => 'Pelanggan Baru',
            'email'                 => 'pelangganbaru@rhs.id',
            'password'              => 'ValidCustomerPass123!',
            'password_confirmation' => 'ValidCustomerPass123!',
        ]);
        $regResponse->assertRedirect(route('dashboard'));

        $newCustomer = User::where('email', 'pelangganbaru@rhs.id')->first();
        $this->assertNotNull($newCustomer);
        $this->assertTrue($newCustomer->isCustomer());

        // 2. Akses dashboard customer
        $this->actingAs($newCustomer)->get(route('customer.dashboard'))->assertStatus(200);

        // 3. Akses dashboard admin oleh admin
        $admin = $this->createAdmin();
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertStatus(200);

        // 4. Akses halaman keranjang dan checkout
        $this->actingAs($newCustomer)->get('/keranjang')->assertStatus(200);
        $this->actingAs($newCustomer)->get('/checkout')->assertRedirect(route('catalog.index'));
    }

    /**
     * 24. Admin dapat mengganti password sendiri via Profil dengan validasi ketat.
     */
    public function test_24_admin_can_update_own_password_with_identical_strict_rules(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->put('/password', [
            'current_password'      => 'AdminSecret12345',
            'password'              => 'NewAdminSecretPass123!',
            'password_confirmation' => 'NewAdminSecretPass123!',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertTrue(Hash::check('NewAdminSecretPass123!', $admin->fresh()->password));
        $this->assertTrue($admin->fresh()->isAdmin());
    }
}
