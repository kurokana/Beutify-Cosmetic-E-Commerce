<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Repositories\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CachingTest extends TestCase
{
    use RefreshDatabase;

    private ProductRepository $productRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productRepository = app(ProductRepository::class);
        Cache::flush(); // Clear all caches before each test
    }

    /**
     * Test that categories are cached for 60 minutes.
     * Requirements: 14.7
     */
    public function test_categories_are_cached_for_60_minutes(): void
    {
        // Create test categories
        Category::factory()->count(3)->create();

        // First call should hit the database
        $categories1 = $this->productRepository->getAllCategories();
        $this->assertCount(3, $categories1);

        // Verify cache exists
        $this->assertTrue(Cache::has('categories_all'));

        // Second call should use cache
        $categories2 = $this->productRepository->getAllCategories();
        $this->assertEquals($categories1->pluck('id'), $categories2->pluck('id'));

        // Create a new category
        Category::factory()->create();

        // Cache should still return old data (not invalidated by category creation)
        $categories3 = $this->productRepository->getAllCategories();
        $this->assertCount(3, $categories3);
    }

    /**
     * Test that brands are cached for 60 minutes.
     * Requirements: 14.7
     */
    public function test_brands_are_cached_for_60_minutes(): void
    {
        // Create test brands
        Brand::factory()->count(3)->create(['is_active' => true]);
        Brand::factory()->create(['is_active' => false]); // Inactive brand

        // First call should hit the database
        $brands1 = $this->productRepository->getAllBrands();
        $this->assertCount(3, $brands1); // Only active brands

        // Verify cache exists
        $this->assertTrue(Cache::has('brands_all_active'));

        // Second call should use cache
        $brands2 = $this->productRepository->getAllBrands();
        $this->assertEquals($brands1->pluck('id'), $brands2->pluck('id'));
    }

    /**
     * Test that best sellers are cached for 60 minutes.
     * Requirements: 14.7
     */
    public function test_best_sellers_are_cached(): void
    {
        $category = Category::factory()->create();
        $brand = Brand::factory()->create(['is_active' => true]);

        // Create products with different order counts
        $product1 = Product::factory()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'is_active' => true,
        ]);
        $product2 = Product::factory()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'is_active' => true,
        ]);

        // Create orders with items
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        OrderItem::factory()->count(5)->create(['order_id' => $order->id, 'product_id' => $product1->id]);
        OrderItem::factory()->count(2)->create(['order_id' => $order->id, 'product_id' => $product2->id]);

        // First call should hit the database
        $bestSellers1 = $this->productRepository->getBestSellers(8);
        $this->assertCount(2, $bestSellers1);
        $this->assertEquals($product1->id, $bestSellers1->first()->id); // Product1 has more orders

        // Verify cache exists
        $this->assertTrue(Cache::has('best_sellers'));

        // Second call should use cache
        $bestSellers2 = $this->productRepository->getBestSellers(8);
        $this->assertEquals($bestSellers1->pluck('id'), $bestSellers2->pluck('id'));
    }

    /**
     * Test that latest products are cached for 30 minutes.
     * Requirements: 14.7
     */
    public function test_latest_products_are_cached(): void
    {
        $category = Category::factory()->create();
        $brand = Brand::factory()->create(['is_active' => true]);

        // Create products at different times
        $product1 = Product::factory()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'is_active' => true,
            'created_at' => now()->subDays(2),
        ]);
        $product2 = Product::factory()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'is_active' => true,
            'created_at' => now()->subDay(),
        ]);

        // First call should hit the database
        $latestProducts1 = $this->productRepository->getLatestProducts(8);
        $this->assertCount(2, $latestProducts1);
        $this->assertEquals($product2->id, $latestProducts1->first()->id); // Product2 is newer

        // Verify cache exists
        $this->assertTrue(Cache::has('latest_products'));

        // Second call should use cache
        $latestProducts2 = $this->productRepository->getLatestProducts(8);
        $this->assertEquals($latestProducts1->pluck('id'), $latestProducts2->pluck('id'));
    }

    /**
     * Test that featured brands are cached for 60 minutes.
     * Requirements: 14.7
     */
    public function test_featured_brands_are_cached(): void
    {
        // Create brands with and without logos
        Brand::factory()->count(2)->create(['is_active' => true, 'logo_path' => 'brands/logo.png']);
        Brand::factory()->create(['is_active' => true, 'logo_path' => null]); // No logo
        Brand::factory()->create(['is_active' => false, 'logo_path' => 'brands/logo2.png']); // Inactive

        // First call should hit the database
        $featuredBrands1 = $this->productRepository->getFeaturedBrands();
        $this->assertCount(2, $featuredBrands1); // Only active brands with logos

        // Verify cache exists
        $this->assertTrue(Cache::has('featured_brands'));

        // Second call should use cache
        $featuredBrands2 = $this->productRepository->getFeaturedBrands();
        $this->assertEquals($featuredBrands1->pluck('id'), $featuredBrands2->pluck('id'));
    }

    /**
     * Test that cache is invalidated when a product is created.
     * Requirements: 14.7
     */
    public function test_cache_invalidated_when_product_created(): void
    {
        $category = Category::factory()->create();
        $brand = Brand::factory()->create(['is_active' => true]);

        // Prime the caches
        $this->productRepository->getLatestProducts(8);
        $this->productRepository->getBestSellers(8);

        $this->assertTrue(Cache::has('latest_products'));
        $this->assertTrue(Cache::has('best_sellers'));

        // Create a new product (as admin to trigger observer)
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        Product::factory()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'is_active' => true,
        ]);

        // Caches should be invalidated
        $this->assertFalse(Cache::has('latest_products'));
        $this->assertFalse(Cache::has('best_sellers'));
    }

    /**
     * Test that cache is invalidated when a product is updated.
     * Requirements: 14.7
     */
    public function test_cache_invalidated_when_product_updated(): void
    {
        $category = Category::factory()->create();
        $brand = Brand::factory()->create(['is_active' => true]);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'is_active' => true,
        ]);

        // Prime the caches
        $this->productRepository->getLatestProducts(8);
        $this->productRepository->getBestSellers(8);

        $this->assertTrue(Cache::has('latest_products'));
        $this->assertTrue(Cache::has('best_sellers'));

        // Update the product (as admin to trigger observer)
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $product->update(['name' => 'Updated Product Name']);

        // Caches should be invalidated
        $this->assertFalse(Cache::has('latest_products'));
        $this->assertFalse(Cache::has('best_sellers'));
    }

    /**
     * Test that cache is invalidated when a product is deleted.
     * Requirements: 14.7
     */
    public function test_cache_invalidated_when_product_deleted(): void
    {
        $category = Category::factory()->create();
        $brand = Brand::factory()->create(['is_active' => true]);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'is_active' => true,
        ]);

        // Prime the caches
        $this->productRepository->getLatestProducts(8);
        $this->productRepository->getBestSellers(8);

        $this->assertTrue(Cache::has('latest_products'));
        $this->assertTrue(Cache::has('best_sellers'));

        // Delete the product (as admin to trigger observer)
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $product->delete();

        // Caches should be invalidated
        $this->assertFalse(Cache::has('latest_products'));
        $this->assertFalse(Cache::has('best_sellers'));
    }

    /**
     * Test that cache is invalidated when a review is created.
     * Requirements: 14.7
     */
    public function test_cache_invalidated_when_review_created(): void
    {
        $category = Category::factory()->create();
        $brand = Brand::factory()->create(['is_active' => true]);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'is_active' => true,
        ]);

        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'delivered']);
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id]);

        // Prime the caches
        $this->productRepository->getBestSellers(8);
        Cache::put("product_avg_rating_{$product->id}", 4.5, 3600);

        $this->assertTrue(Cache::has('best_sellers'));
        $this->assertTrue(Cache::has("product_avg_rating_{$product->id}"));

        // Create a review
        Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Great product!',
        ]);

        // Caches should be invalidated
        $this->assertFalse(Cache::has('best_sellers'));
        $this->assertFalse(Cache::has("product_avg_rating_{$product->id}"));
    }

    /**
     * Test that cache is invalidated when a review is deleted.
     * Requirements: 14.7
     */
    public function test_cache_invalidated_when_review_deleted(): void
    {
        $category = Category::factory()->create();
        $brand = Brand::factory()->create(['is_active' => true]);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'is_active' => true,
        ]);

        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'delivered']);
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id]);

        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Great product!',
        ]);

        // Prime the caches
        $this->productRepository->getBestSellers(8);
        Cache::put("product_avg_rating_{$product->id}", 4.5, 3600);

        $this->assertTrue(Cache::has('best_sellers'));
        $this->assertTrue(Cache::has("product_avg_rating_{$product->id}"));

        // Delete the review
        $review->delete();

        // Caches should be invalidated
        $this->assertFalse(Cache::has('best_sellers'));
        $this->assertFalse(Cache::has("product_avg_rating_{$product->id}"));
    }

    /**
     * Test that homepage loads with cached data.
     * Requirements: 13.1, 14.7
     */
    public function test_homepage_loads_with_cached_data(): void
    {
        $category = Category::factory()->create();
        $brand = Brand::factory()->create(['is_active' => true, 'logo_path' => 'brands/logo.png']);
        Product::factory()->count(3)->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'is_active' => true,
        ]);

        // First request should cache the data
        $response = $this->get('/');
        $response->assertStatus(200);

        // Verify caches exist
        $this->assertTrue(Cache::has('featured_brands'));
        $this->assertTrue(Cache::has('latest_products'));
        $this->assertTrue(Cache::has('best_sellers'));

        // Second request should use cached data
        $response2 = $this->get('/');
        $response2->assertStatus(200);
    }
}
