<?php

namespace Tests\Unit;

use App\Models\Address;
use App\Models\User;
use App\Services\RajaOngkirClient;
use App\Services\ShippingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ShippingServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that ShippingService is properly instantiated.
     *
     * Requirements: 4.3, 6.1
     *
     * @return void
     */
    public function test_shipping_service_is_configured(): void
    {
        $client = $this->createMock(RajaOngkirClient::class);
        $service = new ShippingService($client);

        $this->assertInstanceOf(ShippingService::class, $service);
    }

    /**
     * Test that calculateShippingCost returns shipping options.
     *
     * Requirements: 4.3, 6.1
     *
     * @return void
     */
    public function test_calculate_shipping_cost_returns_options(): void
    {
        // Create test data
        $user = User::factory()->create();
        $address = Address::factory()->create([
            'user_id' => $user->id,
            'city' => '501', // Numeric city ID for testing
        ]);

        // Mock RajaOngkirClient
        $client = $this->createMock(RajaOngkirClient::class);
        $client->method('getCost')
            ->willReturn([
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

        $service = new ShippingService($client);

        // Clear cache before test
        Cache::flush();

        $options = $service->calculateShippingCost($address, 1000);

        $this->assertIsArray($options);
        $this->assertNotEmpty($options);
        $this->assertArrayHasKey('courier_name', $options[0]);
        $this->assertArrayHasKey('service', $options[0]);
        $this->assertArrayHasKey('cost', $options[0]);
    }

    /**
     * Test that calculateShippingCost caches results.
     *
     * Requirements: 4.3, 6.1
     *
     * @return void
     */
    public function test_calculate_shipping_cost_caches_results(): void
    {
        // Create test data
        $user = User::factory()->create();
        $address = Address::factory()->create([
            'user_id' => $user->id,
            'city' => '501',
        ]);

        // Mock RajaOngkirClient
        $client = $this->createMock(RajaOngkirClient::class);
        // Should be called 3 times (once per courier: jne, jnt, sicepat) on first call
        // and 0 times on second call due to caching
        $client->expects($this->exactly(3))
            ->method('getCost')
            ->willReturn([
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

        $service = new ShippingService($client);

        // Clear cache before test
        Cache::flush();

        // First call - should hit the API (3 times, once per courier)
        $options1 = $service->calculateShippingCost($address, 1000);

        // Second call - should use cache (no API calls)
        $options2 = $service->calculateShippingCost($address, 1000);

        $this->assertEquals($options1, $options2);
    }

    /**
     * Test that calculateShippingCost throws exception when API fails.
     *
     * Requirements: 4.4
     *
     * @return void
     */
    public function test_calculate_shipping_cost_throws_exception_on_api_failure(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tidak dapat menghitung ongkos kirim');

        // Create test data
        $user = User::factory()->create();
        $address = Address::factory()->create([
            'user_id' => $user->id,
            'city' => '501',
        ]);

        // Mock RajaOngkirClient to throw exception
        $client = $this->createMock(RajaOngkirClient::class);
        $client->method('getCost')
            ->willThrowException(new \Exception('API error'));

        $service = new ShippingService($client);

        // Clear cache before test
        Cache::flush();

        $service->calculateShippingCost($address, 1000);
    }

    /**
     * Test that trackShipment returns tracking information.
     *
     * Requirements: 6.4, 6.5
     *
     * @return void
     */
    public function test_track_shipment_returns_tracking_info(): void
    {
        // Mock RajaOngkirClient
        $client = $this->createMock(RajaOngkirClient::class);
        $client->method('trackShipment')
            ->willReturn([
                'rajaongkir' => [
                    'result' => [
                        'waybill_number' => 'JNE1234567890',
                        'courier_name' => 'JNE',
                        'service_code' => 'REG',
                        'status' => 'DELIVERED',
                        'receiver_name' => 'John Doe',
                        'manifest' => [
                            [
                                'manifest_date' => '2024-01-01 10:00:00',
                                'manifest_description' => 'Paket telah diterima',
                            ],
                        ],
                    ],
                ],
            ]);

        $service = new ShippingService($client);

        $tracking = $service->trackShipment('JNE1234567890', 'jne');

        $this->assertIsArray($tracking);
        $this->assertTrue($tracking['found']);
        $this->assertEquals('JNE1234567890', $tracking['waybill']);
        $this->assertEquals('JNE', $tracking['courier']);
    }

    /**
     * Test that trackShipment handles not found case.
     *
     * Requirements: 6.5
     *
     * @return void
     */
    public function test_track_shipment_handles_not_found(): void
    {
        // Mock RajaOngkirClient
        $client = $this->createMock(RajaOngkirClient::class);
        $client->method('trackShipment')
            ->willReturn([
                'rajaongkir' => [],
            ]);

        $service = new ShippingService($client);

        $tracking = $service->trackShipment('INVALID123', 'jne');

        $this->assertIsArray($tracking);
        $this->assertFalse($tracking['found']);
        $this->assertArrayHasKey('message', $tracking);
    }
}
