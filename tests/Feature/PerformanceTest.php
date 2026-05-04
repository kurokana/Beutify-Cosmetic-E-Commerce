<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that catalog page loads in less than 3 seconds.
     * Requirements: 14.6
     */
    public function test_catalog_page_loads_within_3_seconds(): void
    {
        // Create test data
        $category = Category::factory()->create();
        $brand = Brand::factory()->create(['is_active' => true]);
        Product::factory()->count(50)->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'is_active' => true,
        ]);

        // Prime the caches
        Cache::flush();

        // Measure first load time (cold cache)
        $startTime = microtime(true);
        $response = $this->get('/catalog');
        $firstLoadTime = microtime(true) - $startTime;

        $response->assertStatus(200);
        $this->assertLessThan(3.0, $firstLoadTime, "First catalog load took {$firstLoadTime}s, should be < 3s");

        // Measure second load time (warm cache)
        $startTime = microtime(true);
        $response2 = $this->get('/catalog');
        $secondLoadTime = microtime(true) - $startTime;

        $response2->assertStatus(200);
        $this->assertLessThan(3.0, $secondLoadTime, "Second catalog load took {$secondLoadTime}s, should be < 3s");
        $this->assertLessThan($firstLoadTime, $secondLoadTime, "Cached load should be faster than first load");
    }

    /**
     * Test that homepage loads in less than 3 seconds.
     * Requirements: 14.6
     */
    public function test_homepage_loads_within_3_seconds(): void
    {
        // Create test data
        $category = Category::factory()->create();
        $brand = Brand::factory()->create(['is_active' => true, 'logo_path' => 'brands/logo.png']);
        Product::factory()->count(30)->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'is_active' => true,
        ]);

        // Prime the caches
        Cache::flush();

        // Measure first load time (cold cache)
        $startTime = microtime(true);
        $response = $this->get('/');
        $firstLoadTime = microtime(true) - $startTime;

        $response->assertStatus(200);
        $this->assertLessThan(3.0, $firstLoadTime, "First homepage load took {$firstLoadTime}s, should be < 3s");

        // Measure second load time (warm cache)
        $startTime = microtime(true);
        $response2 = $this->get('/');
        $secondLoadTime = microtime(true) - $startTime;

        $response2->assertStatus(200);
        $this->assertLessThan(3.0, $secondLoadTime, "Second homepage load took {$secondLoadTime}s, should be < 3s");
        $this->assertLessThan($firstLoadTime, $secondLoadTime, "Cached load should be faster than first load");
    }

    /**
     * Test that search results load in less than 500ms.
     * Requirements: 2.4
     */
    public function test_search_results_load_within_500ms(): void
    {
        // Create test data
        $category = Category::factory()->create();
        $brand = Brand::factory()->create(['is_active' => true, 'name' => 'TestBrand']);
        Product::factory()->count(100)->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'is_active' => true,
            'name' => 'Test Product',
        ]);

        // Measure search time
        $startTime = microtime(true);
        $response = $this->get('/catalog?keyword=Test');
        $searchTime = microtime(true) - $startTime;

        $response->assertStatus(200);
        $this->assertLessThan(0.5, $searchTime, "Search took {$searchTime}s, should be < 500ms");
    }

    /**
     * Test that product detail page loads quickly.
     * Requirements: 14.6
     */
    public function test_product_detail_page_loads_quickly(): void
    {
        // Create test data
        $category = Category::factory()->create();
        $brand = Brand::factory()->create(['is_active' => true]);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'is_active' => true,
        ]);

        // Measure load time
        $startTime = microtime(true);
        $response = $this->get("/catalog/{$product->slug}");
        $loadTime = microtime(true) - $startTime;

        $response->assertStatus(200);
        $this->assertLessThan(3.0, $loadTime, "Product detail load took {$loadTime}s, should be < 3s");
    }

    /**
     * Test that filtered catalog loads efficiently.
     * Requirements: 14.6, 14.7
     */
    public function test_filtered_catalog_loads_efficiently(): void
    {
        // Create test data
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        $brand = Brand::factory()->create(['is_active' => true]);

        Product::factory()->count(30)->create([
            'category_id' => $category1->id,
            'brand_id' => $brand->id,
            'is_active' => true,
            'price' => 100000,
        ]);

        Product::factory()->count(20)->create([
            'category_id' => $category2->id,
            'brand_id' => $brand->id,
            'is_active' => true,
            'price' => 200000,
        ]);

        // Test category filter
        $startTime = microtime(true);
        $response = $this->get("/catalog?category_id={$category1->id}");
        $filterTime = microtime(true) - $startTime;

        $response->assertStatus(200);
        $this->assertLessThan(3.0, $filterTime, "Filtered catalog load took {$filterTime}s, should be < 3s");

        // Test price filter
        $startTime = microtime(true);
        $response2 = $this->get('/catalog?min_price=50000&max_price=150000');
        $priceFilterTime = microtime(true) - $startTime;

        $response2->assertStatus(200);
        $this->assertLessThan(3.0, $priceFilterTime, "Price filtered catalog load took {$priceFilterTime}s, should be < 3s");
    }
}
