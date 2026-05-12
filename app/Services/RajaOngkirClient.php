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

    /**
     * Get list of provinces.
     *
     * @return array
     * @throws \Exception
     */
    public function getProvinces(): array
    {
        $response = Http::withHeaders(['key' => $this->apiKey])->get("{$this->baseUrl}/province");

        if ($response->failed()) {
            Log::error('RajaOngkir provinces error', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \Exception('Gagal memuat daftar provinsi.');
        }

        $data = $response->json();

        return $data['rajaongkir']['results'] ?? [];
    }

    /**
     * Get list of cities within a province.
     *
     * @param int|string $provinceId
     * @return array
     * @throws \Exception
     */
    public function getCities($provinceId): array
    {
        $response = Http::withHeaders(['key' => $this->apiKey])->get("{$this->baseUrl}/city", ['province' => $provinceId]);

        if ($response->failed()) {
            Log::error('RajaOngkir cities error', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \Exception('Gagal memuat daftar kota.');
        }

        $data = $response->json();

        return $data['rajaongkir']['results'] ?? [];
    }

    /**
     * Get list of subdistricts (kecamatan) for a city.
     * Note: availability depends on RajaOngkir plan. If not available, returns empty.
     *
     * @param int|string $cityId
     * @return array
     * @throws \Exception
     */
    public function getSubdistricts($cityId): array
    {
        // Some RajaOngkir plans support /subdistrict endpoint
        try {
            $response = Http::withHeaders(['key' => $this->apiKey])->get("{$this->baseUrl}/subdistrict", ['city' => $cityId]);

            if ($response->failed()) {
                Log::warning('RajaOngkir subdistricts API failed', ['status' => $response->status(), 'body' => $response->body()]);
                return [];
            }

            $data = $response->json();

            return $data['rajaongkir']['results'] ?? [];
        } catch (\Throwable $e) {
            Log::warning('RajaOngkir subdistricts exception', ['message' => $e->getMessage()]);
            return [];
        }
    }
}
