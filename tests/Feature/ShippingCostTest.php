<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\RajaOngkirClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingCostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that authenticated user can calculate shipping cost.
     *
     * Requirements: 4.3, 6.1
     *
     * @return void
     */
    public function test_authenticated_user_can_calculate_shipping_cost(): void
    {
        // Create test data
        $user = User::factory()->create();
        $address = Address::factory()->create([
            'user_id' => $user->id,
            'city' => '501',
        ]);
        $product = Product::factory()->create([
            'weight' => 500,
            'stock' => 10,
        ]);
        CartItem::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        // Mock RajaOngkirClient
        $this->mock(RajaOngkirClient::class, function ($mock) {
            $mock->shouldReceive('getCost')
                ->andReturn([
                    'rajaongkir' => [
                        'results' => [
                            [
                                'costs' => [
                                    [
                                        'service' => 'REG',
                                        'description' => 'Regular',
                                        'cost' => [
                                            [
                                                'value' => 15000,
                                                'etd' => '2-3',
                                                'note' => '',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]);
        });

        // Make request
        $response = $this->actingAs($user)->getJson('/shipping/cost?address_id=' . $address->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'courier_name',
                        'service',
                        'cost',
                        'etd',
                    ],
                ],
            ]);
    }

    /**
     * Test that unauthenticated user cannot calculate shipping cost.
     *
     * Requirements: 4.3
     *
     * @return void
     */
    public function test_unauthenticated_user_cannot_calculate_shipping_cost(): void
    {
        $response = $this->getJson('/shipping/cost?address_id=1');

        $response->assertStatus(401);
    }

    /**
     * Test that user cannot calculate shipping cost for another user's address.
     *
     * Requirements: 4.3
     *
     * @return void
     */
    public function test_user_cannot_calculate_shipping_cost_for_another_users_address(): void
    {
        // Create test data
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $address = Address::factory()->create([
            'user_id' => $user2->id,
        ]);

        // Make request as user1 with user2's address
        $response = $this->actingAs($user1)->getJson('/shipping/cost?address_id=' . $address->id);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Alamat tidak valid.',
            ]);
    }

    /**
     * Test that shipping cost calculation fails when cart is empty.
     *
     * Requirements: 4.3
     *
     * @return void
     */
    public function test_shipping_cost_calculation_fails_when_cart_is_empty(): void
    {
        // Create test data
        $user = User::factory()->create();
        $address = Address::factory()->create([
            'user_id' => $user->id,
        ]);

        // Make request with empty cart
        $response = $this->actingAs($user)->getJson('/shipping/cost?address_id=' . $address->id);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Keranjang belanja kosong.',
            ]);
    }

    /**
     * Test that shipping cost calculation returns error when API fails.
     *
     * Requirements: 4.4
     *
     * @return void
     */
    public function test_shipping_cost_calculation_returns_error_when_api_fails(): void
    {
        // Create test data
        $user = User::factory()->create();
        $address = Address::factory()->create([
            'user_id' => $user->id,
            'city' => '501',
        ]);
        $product = Product::factory()->create([
            'weight' => 500,
            'stock' => 10,
        ]);
        CartItem::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        // Mock RajaOngkirClient to throw exception
        $this->mock(RajaOngkirClient::class, function ($mock) {
            $mock->shouldReceive('getCost')
                ->andThrow(new \Exception('Gagal menghubungi API RajaOngkir. Silakan coba lagi.'));
        });

        // Make request
        $response = $this->actingAs($user)->getJson('/shipping/cost?address_id=' . $address->id);

        $response->assertStatus(500)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'success',
                'message',
            ]);
    }

    /**
     * Test that shipping cost calculation requires valid address_id.
     *
     * Requirements: 4.3
     *
     * @return void
     */
    public function test_shipping_cost_calculation_requires_valid_address_id(): void
    {
        $user = User::factory()->create();

        // Make request without address_id
        $response = $this->actingAs($user)->getJson('/shipping/cost');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['address_id']);

        // Make request with non-existent address_id
        $response = $this->actingAs($user)->getJson('/shipping/cost?address_id=99999');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['address_id']);
    }
}
