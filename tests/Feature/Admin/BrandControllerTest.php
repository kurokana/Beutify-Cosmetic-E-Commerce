<?php

namespace Tests\Feature\Admin;

use App\Models\Brand;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Fake storage
        Storage::fake('public');
    }

    /** @test */
    public function admin_can_view_brands_index()
    {
        $brand = Brand::factory()->create(['name' => 'Test Brand']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.brands.index'));

        $response->assertOk();
        $response->assertSee('Test Brand');
    }

    /** @test */
    public function brands_index_displays_product_count()
    {
        $brand = Brand::factory()->create();
        Product::factory()->count(3)->create([
            'brand_id' => $brand->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.brands.index'));

        $response->assertOk();
        $response->assertSee('3 produk aktif');
    }

    /** @test */
    public function admin_can_view_create_brand_form()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.brands.create'));

        $response->assertOk();
        $response->assertSee('Tambah Merek Baru');
    }

    /** @test */
    public function admin_can_create_brand_without_logo()
    {
        $brandData = [
            'name' => 'New Brand',
            'description' => 'Brand description',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.brands.store'), $brandData);

        $response->assertRedirect(route('admin.brands.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('brands', [
            'name' => 'New Brand',
            'description' => 'Brand description',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function admin_can_create_brand_with_logo()
    {
        $logo = UploadedFile::fake()->image('brand-logo.jpg', 400, 400);

        $brandData = [
            'name' => 'Brand With Logo',
            'description' => 'Brand description',
            'logo' => $logo,
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.brands.store'), $brandData);

        $response->assertRedirect(route('admin.brands.index'));
        $response->assertSessionHas('success');

        $brand = Brand::where('name', 'Brand With Logo')->first();
        $this->assertNotNull($brand);
        $this->assertNotNull($brand->logo_path);
        Storage::disk('public')->assertExists($brand->logo_path);
    }

    /** @test */
    public function brand_name_must_be_unique()
    {
        Brand::factory()->create(['name' => 'Existing Brand']);

        $brandData = [
            'name' => 'Existing Brand',
            'description' => 'Another description',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.brands.store'), $brandData);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function logo_must_be_valid_image_format()
    {
        $invalidFile = UploadedFile::fake()->create('document.pdf', 1000);

        $brandData = [
            'name' => 'Test Brand',
            'logo' => $invalidFile,
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.brands.store'), $brandData);

        $response->assertSessionHasErrors('logo');
    }

    /** @test */
    public function logo_must_not_exceed_size_limit()
    {
        $largeLogo = UploadedFile::fake()->image('large-logo.jpg')->size(3000); // 3MB

        $brandData = [
            'name' => 'Test Brand',
            'logo' => $largeLogo,
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.brands.store'), $brandData);

        $response->assertSessionHasErrors('logo');
    }

    /** @test */
    public function admin_can_view_edit_brand_form()
    {
        $brand = Brand::factory()->create(['name' => 'Edit Test Brand']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.brands.edit', $brand));

        $response->assertOk();
        $response->assertSee('Edit Test Brand');
    }

    /** @test */
    public function admin_can_update_brand()
    {
        $brand = Brand::factory()->create(['name' => 'Old Name']);

        $updateData = [
            'name' => 'Updated Brand Name',
            'description' => 'Updated description',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.brands.update', $brand), $updateData);

        $response->assertRedirect(route('admin.brands.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'name' => 'Updated Brand Name',
            'description' => 'Updated description',
        ]);
    }

    /** @test */
    public function admin_can_update_brand_logo()
    {
        $oldLogo = UploadedFile::fake()->image('old-logo.jpg');
        $brand = Brand::factory()->create([
            'logo_path' => $oldLogo->store('brands', 'public'),
        ]);

        $newLogo = UploadedFile::fake()->image('new-logo.jpg');

        $updateData = [
            'name' => $brand->name,
            'description' => $brand->description,
            'logo' => $newLogo,
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.brands.update', $brand), $updateData);

        $response->assertRedirect(route('admin.brands.index'));

        $brand->refresh();
        $this->assertNotNull($brand->logo_path);
        Storage::disk('public')->assertExists($brand->logo_path);
    }

    /** @test */
    public function admin_can_toggle_brand_active_status()
    {
        $brand = Brand::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.brands.toggle-active', $brand));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function admin_can_delete_brand_without_products()
    {
        $brand = Brand::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.brands.destroy', $brand));

        $response->assertRedirect(route('admin.brands.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('brands', [
            'id' => $brand->id,
        ]);
    }

    /** @test */
    public function admin_cannot_delete_brand_with_active_products()
    {
        $brand = Brand::factory()->create();
        Product::factory()->count(2)->create([
            'brand_id' => $brand->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.brands.destroy', $brand));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $response->assertSessionHas('error', function ($message) {
            return str_contains($message, '2 produk aktif');
        });

        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
        ]);
    }

    /** @test */
    public function admin_cannot_delete_brand_with_inactive_products()
    {
        $brand = Brand::factory()->create();
        Product::factory()->count(2)->create([
            'brand_id' => $brand->id,
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.brands.destroy', $brand));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $response->assertSessionHas('error', function ($message) {
            return str_contains($message, '2 produk terdaftar');
        });

        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
        ]);
    }

    /** @test */
    public function admin_can_delete_brand_logo()
    {
        $logo = UploadedFile::fake()->image('logo.jpg');
        $brand = Brand::factory()->create([
            'logo_path' => $logo->store('brands', 'public'),
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.brands.delete-logo', $brand));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $brand->refresh();
        $this->assertNull($brand->logo_path);
    }

    /** @test */
    public function non_admin_cannot_access_brand_management()
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($customer)
            ->get(route('admin.brands.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function guest_cannot_access_brand_management()
    {
        $response = $this->get(route('admin.brands.index'));

        $response->assertRedirect(route('login'));
    }
}
