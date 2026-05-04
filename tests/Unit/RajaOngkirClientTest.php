<?php

namespace Tests\Unit;

use App\Services\RajaOngkirClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RajaOngkirClientTest extends TestCase
{
    /**
     * Test that RajaOngkirClient is properly instantiated.
     *
     * Requirements: 6.1, 6.2
     *
     * @return void
     */
    public function test_rajaongkir_client_is_configured(): void
    {
        $client = new RajaOngkirClient();

        $this->assertInstanceOf(RajaOngkirClient::class, $client);
    }

    /**
     * Test that getCost makes successful API call.
     *
     * Requirements: 6.1
     *
     * @return void
     */
    public function test_get_cost_makes_successful_api_call(): void
    {
        // Mock HTTP response
        Http::fake([
            '*/cost' => Http::response([
                'rajaongkir' => [
                    'status' => [
                        'code' => 200,
                        'description' => 'OK',
                    ],
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
            ], 200),
        ]);

        $client = new RajaOngkirClient();
        $result = $client->getCost(501, 114, 1000, 'jne');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('rajaongkir', $result);
        $this->assertEquals(200, $result['rajaongkir']['status']['code']);
    }

    /**
     * Test that getCost throws exception on API error.
     *
     * Requirements: 4.4, 6.1
     *
     * @return void
     */
    public function test_get_cost_throws_exception_on_api_error(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Gagal menghubungi API RajaOngkir');

        // Mock HTTP response with error
        Http::fake([
            '*/cost' => Http::response([], 500),
        ]);

        $client = new RajaOngkirClient();
        $client->getCost(501, 114, 1000, 'jne');
    }

    /**
     * Test that getCost throws exception on connection error.
     *
     * Requirements: 4.4, 6.1
     *
     * @return void
     */
    public function test_get_cost_throws_exception_on_connection_error(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tidak dapat terhubung ke layanan pengiriman');

        // Mock HTTP connection exception
        Http::fake(function () {
            throw new \Illuminate\Http\Client\ConnectionException('Connection failed');
        });

        $client = new RajaOngkirClient();
        $client->getCost(501, 114, 1000, 'jne');
    }

    /**
     * Test that getCost throws exception when RajaOngkir returns error in response body.
     *
     * Requirements: 4.4, 6.1
     *
     * @return void
     */
    public function test_get_cost_throws_exception_on_rajaongkir_error_response(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Gagal menghitung ongkos kirim');

        // Mock HTTP response with RajaOngkir error
        Http::fake([
            '*/cost' => Http::response([
                'rajaongkir' => [
                    'status' => [
                        'code' => 400,
                        'description' => 'Invalid API key',
                    ],
                ],
            ], 200),
        ]);

        $client = new RajaOngkirClient();
        $client->getCost(501, 114, 1000, 'jne');
    }

    /**
     * Test that trackShipment makes successful API call.
     *
     * Requirements: 6.4
     *
     * @return void
     */
    public function test_track_shipment_makes_successful_api_call(): void
    {
        // Mock HTTP response
        Http::fake([
            '*/waybill' => Http::response([
                'rajaongkir' => [
                    'status' => [
                        'code' => 200,
                        'description' => 'OK',
                    ],
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
            ], 200),
        ]);

        $client = new RajaOngkirClient();
        $result = $client->trackShipment('JNE1234567890', 'jne');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('rajaongkir', $result);
        $this->assertEquals(200, $result['rajaongkir']['status']['code']);
    }

    /**
     * Test that trackShipment throws exception on API error.
     *
     * Requirements: 6.5
     *
     * @return void
     */
    public function test_track_shipment_throws_exception_on_api_error(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Gagal melacak pengiriman');

        // Mock HTTP response with error
        Http::fake([
            '*/waybill' => Http::response([], 500),
        ]);

        $client = new RajaOngkirClient();
        $client->trackShipment('JNE1234567890', 'jne');
    }

    /**
     * Test that trackShipment throws exception on connection error.
     *
     * Requirements: 6.5
     *
     * @return void
     */
    public function test_track_shipment_throws_exception_on_connection_error(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tidak dapat terhubung ke layanan pelacakan');

        // Mock HTTP connection exception
        Http::fake(function () {
            throw new \Illuminate\Http\Client\ConnectionException('Connection failed');
        });

        $client = new RajaOngkirClient();
        $client->trackShipment('JNE1234567890', 'jne');
    }
}
