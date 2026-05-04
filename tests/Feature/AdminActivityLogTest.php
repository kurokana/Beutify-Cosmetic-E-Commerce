<?php

namespace Tests\Feature;

use App\Models\AdminLog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminActivityLogTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create customer user
        $this->customer = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_logs_product_creation_by_admin(): void
    {
        $this->actingAs($this->admin);

        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'Test description',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'price' => 100000,
            'stock' => 10,
            'sku' => 'TEST-001',
            'weight' => 100,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('admin_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'created',
            'model_type' => Product::class,
            'model_id' => $product->id,
        ]);

        $log = AdminLog::where('model_id', $product->id)
            ->where('model_type', Product::class)
            ->first();

        $this->assertNotNull($log);
        $this->assertNull($log->old_values);
        $this->assertNotNull($log->new_values);
        $this->assertEquals('Test Product', $log->new_values['name']);
    }

    /** @test */
    public function it_logs_product_update_by_admin(): void
    {
        $this->actingAs($this->admin);

        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $product = Product::factory()->create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'Original Name',
            'price' => 100000,
        ]);

        // Clear the creation log
        AdminLog::truncate();

        // Update the product
        $product->update([
            'name' => 'Updated Name',
            'price' => 150000,
        ]);

        $this->assertDatabaseHas('admin_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'updated',
            'model_type' => Product::class,
            'model_id' => $product->id,
        ]);

        $log = AdminLog::where('model_id', $product->id)
            ->where('model_type', Product::class)
            ->where('action', 'updated')
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals('Original Name', $log->old_values['name']);
        $this->assertEquals('Updated Name', $log->new_values['name']);
        $this->assertEquals(100000, $log->old_values['price']);
        $this->assertEquals(150000, $log->new_values['price']);
    }

    /** @test */
    public function it_logs_product_deletion_by_admin(): void
    {
        $this->actingAs($this->admin);

        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $product = Product::factory()->create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]);

        $productId = $product->id;

        // Clear the creation log
        AdminLog::truncate();

        // Delete the product
        $product->delete();

        $this->assertDatabaseHas('admin_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'deleted',
            'model_type' => Product::class,
            'model_id' => $productId,
        ]);

        $log = AdminLog::where('model_id', $productId)
            ->where('model_type', Product::class)
            ->where('action', 'deleted')
            ->first();

        $this->assertNotNull($log);
        $this->assertNotNull($log->old_values);
        $this->assertNull($log->new_values);
    }

    /** @test */
    public function it_logs_order_status_update_by_admin(): void
    {
        $this->actingAs($this->admin);

        $order = Order::factory()->create([
            'status' => 'pending_payment',
        ]);

        // Clear the creation log
        AdminLog::truncate();

        // Update order status
        $order->update([
            'status' => 'payment_confirmed',
        ]);

        $this->assertDatabaseHas('admin_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'updated',
            'model_type' => Order::class,
            'model_id' => $order->id,
        ]);

        $log = AdminLog::where('model_id', $order->id)
            ->where('model_type', Order::class)
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals('pending_payment', $log->old_values['status']);
        $this->assertEquals('payment_confirmed', $log->new_values['status']);
    }

    /** @test */
    public function it_logs_user_account_status_change_by_admin(): void
    {
        $this->actingAs($this->admin);

        $user = User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
        ]);

        // Clear the creation log
        AdminLog::truncate();

        // Deactivate user
        $user->update([
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('admin_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'updated',
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);

        $log = AdminLog::where('model_id', $user->id)
            ->where('model_type', User::class)
            ->first();

        $this->assertNotNull($log);
        $this->assertTrue($log->old_values['is_active']);
        $this->assertFalse($log->new_values['is_active']);
    }

    /** @test */
    public function it_does_not_log_when_customer_updates_their_profile(): void
    {
        $this->actingAs($this->customer);

        // Customer updates their own profile
        $this->customer->update([
            'name' => 'Updated Customer Name',
        ]);

        // Should not create any admin log
        $this->assertDatabaseMissing('admin_logs', [
            'model_type' => User::class,
            'model_id' => $this->customer->id,
        ]);
    }

    /** @test */
    public function it_redacts_password_in_logs(): void
    {
        $this->actingAs($this->admin);

        $user = User::factory()->create([
            'role' => 'customer',
            'password' => bcrypt('oldpassword'),
        ]);

        // Clear the creation log
        AdminLog::truncate();

        // Update password
        $user->update([
            'password' => bcrypt('newpassword'),
        ]);

        $log = AdminLog::where('model_id', $user->id)
            ->where('model_type', User::class)
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals('[REDACTED]', $log->old_values['password']);
        $this->assertEquals('[REDACTED]', $log->new_values['password']);
    }

    /** @test */
    public function it_includes_timestamp_in_logs(): void
    {
        $this->actingAs($this->admin);

        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $product = Product::factory()->create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]);

        $log = AdminLog::where('model_id', $product->id)
            ->where('model_type', Product::class)
            ->first();

        $this->assertNotNull($log);
        $this->assertNotNull($log->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $log->created_at);
    }

    /** @test */
    public function it_includes_admin_identity_in_logs(): void
    {
        $this->actingAs($this->admin);

        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $product = Product::factory()->create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]);

        $log = AdminLog::where('model_id', $product->id)
            ->where('model_type', Product::class)
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals($this->admin->id, $log->admin_id);
        $this->assertNotNull($log->admin);
        $this->assertEquals($this->admin->email, $log->admin->email);
    }
}
