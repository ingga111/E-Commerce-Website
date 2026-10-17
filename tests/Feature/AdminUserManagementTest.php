<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\DigitalProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DigitalProductSeeder::class);

        $this->admin = User::where('role', UserRole::Admin)->first();
        $this->customer = User::where('role', UserRole::Customer)->first();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.users.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_customer_cannot_access_admin_user_management(): void
    {
        $response = $this->actingAs($this->customer)->get(route('admin.users.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_user_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));
        $response->assertStatus(200);
        $response->assertSeeText('Kelola User & Administrator');
        $response->assertSee($this->admin->email);
        $response->assertSee($this->customer->email);
    }

    public function test_admin_can_view_create_user_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.create'));
        $response->assertStatus(200);
        $response->assertSee('Tambah Pengguna Baru');
        $response->assertSee('Pilih Peran Akun (Role)');
    }

    public function test_admin_can_create_new_admin_user(): void
    {
        $userData = [
            'name' => 'Admin Baru',
            'email' => 'admin.baru@test.com',
            'phone' => '081234567899',
            'role' => 'admin',
            'password' => 'PasswordBaru123@',
            'password_confirmation' => 'PasswordBaru123@',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), $userData);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Admin Baru',
            'email' => 'admin.baru@test.com',
            'role' => 'admin',
        ]);

        $createdUser = User::where('email', 'admin.baru@test.com')->first();
        $this->assertTrue(Hash::check('PasswordBaru123@', $createdUser->password));
        $this->assertTrue($createdUser->isAdmin());
    }

    public function test_admin_can_create_new_customer_user(): void
    {
        $userData = [
            'name' => 'Pelanggan Baru',
            'email' => 'pelanggan.baru@test.com',
            'phone' => '089999999999',
            'role' => 'customer',
            'password' => 'CustomerPass123@',
            'password_confirmation' => 'CustomerPass123@',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), $userData);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Pelanggan Baru',
            'email' => 'pelanggan.baru@test.com',
            'role' => 'customer',
        ]);
    }

    public function test_admin_can_update_existing_user(): void
    {
        $targetUser = User::factory()->create([
            'name' => 'User Lama',
            'email' => 'userlama@test.com',
            'role' => UserRole::Customer,
        ]);

        $updateData = [
            'name' => 'User Lama Diperbarui',
            'email' => 'userlama.updated@test.com',
            'phone' => '087777777777',
            'role' => 'admin',
            'password' => 'NewPassword999@',
            'password_confirmation' => 'NewPassword999@',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $targetUser), $updateData);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'User Lama Diperbarui',
            'email' => 'userlama.updated@test.com',
            'role' => 'admin',
        ]);

        $targetUser->refresh();
        $this->assertTrue(Hash::check('NewPassword999@', $targetUser->password));
    }

    public function test_admin_cannot_demote_themselves(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $this->admin), [
            'name' => $this->admin->name,
            'email' => $this->admin->email,
            'phone' => $this->admin->phone,
            'role' => 'customer',
        ]);

        $response->assertSessionHas('error');
        $this->admin->refresh();
        $this->assertTrue($this->admin->isAdmin());
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', [
            'id' => $this->admin->id,
        ]);
    }

    public function test_admin_can_delete_other_user(): void
    {
        $targetUser = User::factory()->create([
            'name' => 'User Akan Dihapus',
            'email' => 'hapus@test.com',
            'role' => UserRole::Customer,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $targetUser));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', [
            'id' => $targetUser->id,
        ]);
    }
}
