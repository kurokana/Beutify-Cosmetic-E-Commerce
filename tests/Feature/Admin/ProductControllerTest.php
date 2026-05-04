<?php

namespace Tests\Feature\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Brand $brand;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create brand and category
        $this->brand = Brand::factory()->create(['is_active' => true]);
        $this->category = Category::factory()->create();

        // Fake storage
        Storage::fake('public');
    }

    /** @test */
    public function admin_can_view_products_index()
    {
        $product = Product::factory()->create([
            'brand_id' => $this->brand->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.index'));

        $response->assertOk();
        $response->assertSee($product->name);
    }

    /** @test */
    public function admin_can_view_create_product_form()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.create'));

        $response->assertOk();
        $response->assertSee('Tambah Produk Baru');
    }

    /** @test */
    public function admin_can_create_product_with_images()
    {
        $image1 = UploadedFile::fake()->image('product1.jpg', 800, 600);
        $image2 = UploadedFile::fake()->image('product2.jpg', 800, 600);

        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'brand_id' => $this->brand->id,
            'category_id' => $this->category->id,
            'price' => 100000,
            'stock' => 50,
            'sku' => 'TEST-SKU-001',
            'weight' => 100,
            'images' => [$image1, $image2],
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), $productData);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'sku' => 'TEST-SKU-001',
            'price' => 100000,
        ]);

        // Verify images were uploaded
        $product = Product::where('sku', 'TEST-SKU-001')->first();
        $this->assertCount(2, $product->images);
        $this->assertTrue($product->images->first()->is_primary);
    }

    /** @test */
    public function admin_can_view_edit_product_form()
    {
        $product = Product::factory()->create([
            'brand_id' => $this->brand->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.edit', $product));

        $response->assertOk();
        $response->assertSee($product->name);
    }

    /** @test */
    public function admin_can_update_product()
    {
        $product = Product::factory()->create([
            'brand_id' => $this->brand->id,
            'category_id' => $this->category->id,
            'name' => 'Old Name',
        ]);

        $updateData = [
            'name' => 'Updated Product Name',
            'description' => $product->description,
            'brand_id' => $this->brand->id,
            'category_id' => $this->category->id,
            'price' => 150000,
            'stock' => 75,
            'sku' => $product->sku,
            'weight' => $product->weight,
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.products.update', $product), $updateData);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name',
            'price' => 150000,
        ]);
    }

    /** @test */
    public function admin_can_toggle_product_active_status()
    {
        $product = Product::factory()->create([
            'brand_id' => $this->brand->id,
            'category_id' => $this->category->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.products.toggle-active', $product));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function admin_can_delete_product()
    {
        $product = Product::factory()->create([
            'brand_id' => $this->brand->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    /** @test */
    public function non_admin_cannot_access_product_management()
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($customer)
            ->get(route('admin.products.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function guest_cannot_access_product_management()
    {
        $response = $this->get(route('admin.products.index'));

        $response->assertRedirect(route('login'));
    }
}
