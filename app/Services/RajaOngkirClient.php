<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * HTTP client for RajaOngkir API.
 *
 * Requirements: 6.1, 6.2
 */
class RajaOngkirClient
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('rajaongkir.api_key');
        $this->baseUrl = config('rajaongkir.base_url');
    }

    /**
     * Get shipping cost from RajaOngkir API.
     *
     * @param int    $origin      Origin city ID
     * @param int    $destination Destination city ID
     * @param int    $weight      Weight in grams
     * @param string $courier     Courier code (jne, jnt, sicepat)
     *
     * @return array Response from RajaOngkir API
     * @throws \Exception when API call fails
     */
    public function getCost(int $origin, int $destination, int $weight, string $courier): array
    {
        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->asForm()->post("{$this->baseUrl}/cost", [
                'origin'      => $origin,
                'destination' => $destination,
                'weight'      => $weight,
                'courier'     => $courier,
            ]);

            if ($response->failed()) {
                Log::error('RajaOngkir API error', [
                    'status'   => $response->status(),
                    'response' => $response->body(),
                ]);

                throw new \Exception('Gagal menghubungi API RajaOngkir. Silakan coba lagi.');
            }

            $data = $response->json();

            // Check if rajaongkir returns error in response body
            if (isset($data['rajaongkir']['status']['code']) && $data['rajaongkir']['status']['code'] !== 200) {
                Log::error('RajaOngkir API returned error', [
                    'status'      => $data['rajaongkir']['status'],
                    'description' => $data['rajaongkir']['status']['description'] ?? 'Unknown error',
                ]);

                throw new \Exception('Gagal menghitung ongkos kirim. Silakan coba lagi.');
            }

            return $data;
        } catch (ConnectionException $e) {
            Log::error('RajaOngkir connection error', [
                'message' => $e->getMessage(),
            ]);

            throw new \Exception('Tidak dapat terhubung ke layanan pengiriman. Silakan coba lagi.');
        } catch (RequestException $e) {
            Log::error('RajaOngkir request error', [
                'message' => $e->getMessage(),
            ]);

            throw new \Exception('Gagal menghubungi API RajaOngkir. Silakan coba lagi.');
        }
    }

    /**
     * Track shipment using waybill number.
     *
     * @param string $waybill Tracking/waybill number
     * @param string $courier Courier code (jne, jnt, sicepat)
     *
     * @return array Response from RajaOngkir API
     * @throws \Exception when API call fails
     */
    public function trackShipment(string $waybill, string $courier): array
    {
        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->asForm()->post("{$this->baseUrl}/waybill", [
                'waybill' => $waybill,
                'courier' => $courier,
            ]);

            if ($response->failed()) {
                Log::error('RajaOngkir tracking API error', [
                    'status'   => $response->status(),
                    'response' => $response->body(),
                ]);

                throw new \Exception('Gagal melacak pengiriman. Silakan coba lagi.');
            }

            $data = $response->json();

            // Check if rajaongkir returns error in response body
            if (isset($data['rajaongkir']['status']['code']) && $data['rajaongkir']['status']['code'] !== 200) {
                Log::error('RajaOngkir tracking API returned error', [
                    'status'      => $data['rajaongkir']['status'],
                    'description' => $data['rajaongkir']['status']['description'] ?? 'Unknown error',
                ]);

                throw new \Exception('Informasi pelacakan belum tersedia, silakan coba beberapa saat lagi.');
            }

            return $data;
        } catch (ConnectionException $e) {
            Log::error('RajaOngkir tracking connection error', [
                'message' => $e->getMessage(),
            ]);

            throw new \Exception('Tidak dapat terhubung ke layanan pelacakan. Silakan coba lagi.');
        } catch (RequestException $e) {
            Log::error('RajaOngkir tracking request error', [
                'message' => $e->getMessage(),
            ]);

            throw new \Exception('Gagal melacak pengiriman. Silakan coba lagi.');
        }
    }
}
