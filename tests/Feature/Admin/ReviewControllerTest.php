<?php

namespace Tests\Feature\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private Product $product;

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

        // Create brand and category
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        // Create product
        $this->product = Product::factory()->create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'average_rating' => 4.5,
        ]);
    }

    /**
     * Test admin can delete a review and it invalidates product rating cache.
     * Requirements: 7.6
     */
    public function test_admin_can_delete_review_and_invalidate_cache(): void
    {
        // Create a delivered order with the product
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'delivered',
        ]);

        // Create a review
        $review = Review::factory()->create([
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Great product!',
        ]);

        // Cache the product rating
        Cache::put("product_avg_rating_{$this->product->id}", 4.5, 3600);

        // Verify cache exists before deletion
        $this->assertTrue(Cache::has("product_avg_rating_{$this->product->id}"));

        // Act as admin and delete the review
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.reviews.destroy', $review));

        // Assert redirect back with success message
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert review is deleted from database
        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);

        // Assert product average rating is recalculated (should be 0 since no reviews left)
        $this->product->refresh();
        $this->assertEquals(0.0, $this->product->average_rating);
    }

    /**
     * Test admin can delete review and average rating is recalculated correctly.
     * Requirements: 7.5, 7.6
     */
    public function test_deleting_review_recalculates_average_rating(): void
    {
        // Create a delivered order
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'delivered',
        ]);

        // Create multiple reviews
        $review1 = Review::factory()->create([
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
            'order_id' => $order->id,
            'rating' => 5,
        ]);

        $anotherCustomer = User::factory()->create(['role' => 'customer']);
        $anotherOrder = Order::factory()->create([
            'user_id' => $anotherCustomer->id,
            'status' => 'delivered',
        ]);

        $review2 = Review::factory()->create([
            'user_id' => $anotherCustomer->id,
            'product_id' => $this->product->id,
            'order_id' => $anotherOrder->id,
            'rating' => 3,
        ]);

        // Initial average should be (5 + 3) / 2 = 4.0
        $this->product->refresh();
        $initialAverage = $this->product->average_rating;

        // Delete one review
        $this->actingAs($this->admin)
            ->delete(route('admin.reviews.destroy', $review1));

        // After deletion, average should be 3.0 (only review2 remains)
        $this->product->refresh();
        $this->assertEquals(3.0, $this->product->average_rating);
    }

    /**
     * Test non-admin users cannot delete reviews.
     * Requirements: 14.5
     */
    public function test_non_admin_cannot_delete_review(): void
    {
        // Create a review
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'delivered',
        ]);

        $review = Review::factory()->create([
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
            'order_id' => $order->id,
            'rating' => 5,
        ]);

        // Try to delete as customer (non-admin)
        $response = $this->actingAs($this->customer)
            ->delete(route('admin.reviews.destroy', $review));

        // Assert forbidden or redirect
        $response->assertStatus(403);

        // Assert review still exists
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
        ]);
    }

    /**
     * Test guest users cannot delete reviews.
     * Requirements: 14.5
     */
    public function test_guest_cannot_delete_review(): void
    {
        // Create a review
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'delivered',
        ]);

        $review = Review::factory()->create([
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
            'order_id' => $order->id,
            'rating' => 5,
        ]);

        // Try to delete as guest
        $response = $this->delete(route('admin.reviews.destroy', $review));

        // Assert redirect to login
        $response->assertRedirect(route('login'));

        // Assert review still exists
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
        ]);
    }
}
