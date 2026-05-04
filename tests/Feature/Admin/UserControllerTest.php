<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create customer user
        $this->customer = User::factory()->create([
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);
    }

    /** @test */
    public function admin_can_view_users_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee('Pengguna');
        $response->assertSee('Kelola akun pelanggan terdaftar');
    }

    /** @test */
    public function users_index_displays_customer_information()
    {
        $customer = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'customer',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee('John Doe');
        $response->assertSee('john@example.com');
    }

    /** @test */
    public function users_index_displays_registration_date()
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'created_at' => now()->subDays(5),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee($customer->created_at->format('d M Y'));
    }

    /** @test */
    public function users_index_displays_order_count()
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        // Create 3 orders for this customer
        Order::factory()->count(3)->create([
            'user_id' => $customer->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee('3 pesanan');
    }

    /** @test */
    public function users_index_displays_account_status()
    {
        $activeCustomer = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
        ]);

        $inactiveCustomer = User::factory()->create([
            'role' => 'customer',
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee('Aktif');
        $response->assertSee('Nonaktif');
    }

    /** @test */
    public function users_index_only_shows_customers_not_admins()
    {
        $anotherAdmin = User::factory()->create([
            'name' => 'Another Admin',
            'role' => 'admin',
        ]);

        $customer = User::factory()->create([
            'name' => 'Customer User',
            'role' => 'customer',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee('Customer User');
        $response->assertDontSee('Another Admin');
    }

    /** @test */
    public function admin_can_search_users_by_name()
    {
        $customer1 = User::factory()->create([
            'name' => 'John Doe',
            'role' => 'customer',
        ]);

        $customer2 = User::factory()->create([
            'name' => 'Jane Smith',
            'role' => 'customer',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['search' => 'John']));

        $response->assertOk();
        $response->assertSee('John Doe');
        $response->assertDontSee('Jane Smith');
    }

    /** @test */
    public function admin_can_search_users_by_email()
    {
        $customer1 = User::factory()->create([
            'email' => 'john@example.com',
            'role' => 'customer',
        ]);

        $customer2 = User::factory()->create([
            'email' => 'jane@example.com',
            'role' => 'customer',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['search' => 'john@']));

        $response->assertOk();
        $response->assertSee('john@example.com');
        $response->assertDontSee('jane@example.com');
    }

    /** @test */
    public function admin_can_filter_users_by_active_status()
    {
        $activeCustomer = User::factory()->create([
            'name' => 'Active User',
            'role' => 'customer',
            'is_active' => true,
        ]);

        $inactiveCustomer = User::factory()->create([
            'name' => 'Inactive User',
            'role' => 'customer',
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['status' => 'active']));

        $response->assertOk();
        $response->assertSee('Active User');
        $response->assertDontSee('Inactive User');
    }

    /** @test */
    public function admin_can_filter_users_by_inactive_status()
    {
        $activeCustomer = User::factory()->create([
            'name' => 'Active User',
            'role' => 'customer',
            'is_active' => true,
        ]);

        $inactiveCustomer = User::factory()->create([
            'name' => 'Inactive User',
            'role' => 'customer',
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['status' => 'inactive']));

        $response->assertOk();
        $response->assertSee('Inactive User');
        $response->assertDontSee('Active User');
    }

    /** @test */
    public function admin_can_deactivate_customer_account()
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.users.toggle-active', $customer));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Akun pelanggan berhasil dinonaktifkan.');

        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function admin_can_activate_customer_account()
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.users.toggle-active', $customer));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Akun pelanggan berhasil diaktifkan.');

        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function admin_cannot_deactivate_their_own_account()
    {
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.users.toggle-active', $this->admin));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');

        $this->assertDatabaseHas('users', [
            'id' => $this->admin->id,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function admin_cannot_deactivate_other_admin_accounts()
    {
        $anotherAdmin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.users.toggle-active', $anotherAdmin));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Tidak dapat mengubah status akun admin lain.');

        $this->assertDatabaseHas('users', [
            'id' => $anotherAdmin->id,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function non_admin_cannot_access_users_index()
    {
        $response = $this->actingAs($this->customer)
            ->get(route('admin.users.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function non_admin_cannot_toggle_user_status()
    {
        $anotherCustomer = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->customer)
            ->patch(route('admin.users.toggle-active', $anotherCustomer));

        $response->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $anotherCustomer->id,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function guest_cannot_access_users_index()
    {
        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function guest_cannot_toggle_user_status()
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
        ]);

        $response = $this->patch(route('admin.users.toggle-active', $customer));

        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function users_index_shows_email_verification_status()
    {
        $verifiedCustomer = User::factory()->create([
            'name' => 'Verified User',
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        $unverifiedCustomer = User::factory()->create([
            'name' => 'Unverified User',
            'role' => 'customer',
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee('Terverifikasi');
        $response->assertSee('Belum Terverifikasi');
    }

    /** @test */
    public function users_index_is_paginated()
    {
        // Create 25 customers (more than the 20 per page limit)
        User::factory()->count(25)->create([
            'role' => 'customer',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertOk();
        // Check that pagination links are present
        $response->assertSee('Pagination Navigation');
    }
}
