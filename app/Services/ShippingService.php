<?php

namespace App\Services;

use App\Models\Address;
use Illuminate\Support\Facades\Cache;

/**
 * Service for handling shipping cost calculation and tracking.
 *
 * Requirements: 4.3, 4.4, 6.1, 6.2
 */
class ShippingService
{
    private RajaOngkirClient $client;
    private int $originCityId;
    private array $supportedCouriers;
    private int $cacheDuration;

    public function __construct(RajaOngkirClient $client)
    {
        $this->client            = $client;
        $this->originCityId      = (int) config('rajaongkir.origin_city_id');
        $this->supportedCouriers = config('rajaongkir.couriers', ['jne', 'jnt', 'sicepat']);
        $this->cacheDuration     = config('rajaongkir.cache_duration', 10);
    }

    /**
     * Calculate shipping cost for all supported couriers.
     *
     * Returns an array of shipping options with courier name, service, cost, and estimate.
     *
     * Requirements: 4.3, 6.1
     *
     * @param Address $address Destination address
     * @param int     $weight  Total weight in grams
     *
     * @return array Array of shipping options
     * @throws \Exception when API call fails
     */
    public function calculateShippingCost(Address $address, int $weight): array
    {
        try {
            $destinationCityId = $this->getCityIdFromAddress($address);
        } catch (\Throwable $e) {
            if ($this->shouldUseRealShippingOnly()) {
                throw $e;
            }

            \Log::warning('Falling back to estimated shipping options', [
                'address_id' => $address->id,
                'message' => $e->getMessage(),
            ]);

            return $this->buildFallbackShippingOptions($address, $weight);
        }

        $allOptions = [];

        foreach ($this->supportedCouriers as $courier) {
            try {
                $options = $this->getShippingOptionsForCourier(
                    $this->originCityId,
                    $destinationCityId,
                    $weight,
                    $courier
                );

                $allOptions = array_merge($allOptions, $options);
            } catch (\Exception $e) {
                // Log error but continue with other couriers
                \Log::warning("Failed to get shipping cost for courier {$courier}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (empty($allOptions)) {
            if ($this->shouldUseRealShippingOnly()) {
                throw new \Exception('Tidak dapat menghitung ongkos kirim. Silakan coba lagi.');
            }

            return $this->buildFallbackShippingOptions($address, $weight);
        }

        return $allOptions;
    }

    /**
     * Get shipping options for a specific courier with caching.
     *
     * Requirements: 4.3, 6.1
     *
     * @param int    $origin      Origin city ID
     * @param int    $destination Destination city ID
     * @param int    $weight      Weight in grams
     * @param string $courier     Courier code
     *
     * @return array Array of shipping options
     * @throws \Exception when API call fails
     */
    private function getShippingOptionsForCourier(
        int $origin,
        int $destination,
        int $weight,
        string $courier
    ): array {
        // Cache key based on origin, destination, weight, and courier
        $cacheKey = "shipping_cost:{$origin}:{$destination}:{$weight}:{$courier}";

        return Cache::remember($cacheKey, now()->addMinutes($this->cacheDuration), function () use (
            $origin,
            $destination,
            $weight,
            $courier
        ) {
            $response = $this->client->getCost($origin, $destination, $weight, $courier);

            return $this->parseShippingResponse($response, $courier);
        });
    }

    /**
     * Parse RajaOngkir API response into a normalized array of shipping options.
     *
     * @param array  $response RajaOngkir API response
     * @param string $courier  Courier code
     *
     * @return array Array of shipping options
     */
    private function parseShippingResponse(array $response, string $courier): array
    {
        $options = [];

        if (!isset($response['rajaongkir']['results'][0]['costs'])) {
            return $options;
        }

        $costs = $response['rajaongkir']['results'][0]['costs'];

        foreach ($costs as $cost) {
            $options[] = [
                'courier_name' => $courier,
                'service'      => $cost['service'] ?? '',
                'description'  => $cost['description'] ?? '',
                'cost'         => $cost['cost'][0]['value'] ?? 0,
                'etd'          => $cost['cost'][0]['etd'] ?? '',
                'note'         => $cost['cost'][0]['note'] ?? '',
            ];
        }

        return $options;
    }

    /**
     * Track shipment using waybill number.
     *
     * Requirements: 6.4, 6.5
     *
     * @param string $waybill Tracking/waybill number
     * @param string $courier Courier code
     *
     * @return array Tracking information
     * @throws \Exception when API call fails
     */
    public function trackShipment(string $waybill, string $courier): array
    {
        $response = $this->client->trackShipment($waybill, $courier);

        return $this->parseTrackingResponse($response);
    }

    /**
     * Parse RajaOngkir tracking API response.
     *
     * @param array $response RajaOngkir API response
     *
     * @return array Tracking information
     */
    private function parseTrackingResponse(array $response): array
    {
        if (!isset($response['rajaongkir']['result'])) {
            return [
                'found'   => false,
                'message' => 'Informasi pelacakan tidak ditemukan.',
            ];
        }

        $result = $response['rajaongkir']['result'];

        return [
            'found'         => true,
            'waybill'       => $result['waybill_number'] ?? '',
            'courier'       => $result['courier_name'] ?? '',
            'service'       => $result['service_code'] ?? '',
            'status'        => $result['status'] ?? '',
            'receiver_name' => $result['receiver_name'] ?? '',
            'manifest'      => $result['manifest'] ?? [],
        ];
    }

    /**
     * Extract city ID from address.
     *
     * This is a placeholder implementation. In a real application, you would:
     * 1. Store the RajaOngkir city_id in the addresses table
     * 2. Or maintain a mapping table between city names and RajaOngkir city IDs
     * 3. Or call RajaOngkir's city API to search for the city
     *
     * For now, we'll return a default city ID or throw an exception.
     *
     * @param Address $address
     *
     * @return int City ID
     * @throws \Exception when city ID cannot be determined
     */
    private function getCityIdFromAddress(Address $address): int
    {
        if (! empty($address->city_id) && is_numeric($address->city_id)) {
            return (int) $address->city_id;
        }

        if (is_numeric($address->city)) {
            return (int) $address->city;
        }

        $resolvedCityId = $this->resolveCityIdFromLegacyAddress($address);
        if ($resolvedCityId !== null) {
            return $resolvedCityId;
        }

        throw new \Exception('Kota tujuan tidak valid. Silakan pilih kota dari daftar yang tersedia.');
    }

    private function resolveCityIdFromLegacyAddress(Address $address): ?int
    {
        try {
            $provinceId = $address->province_id;

            if (empty($provinceId) && ! empty($address->province)) {
                $provinceId = $this->resolveProvinceIdByName($address->province);
            }

            if (empty($provinceId)) {
                return null;
            }

            $cities = $this->client->getCities((int) $provinceId);
            $normalizedTargetCity = $this->normalizeLocationLabel((string) $address->city);

            foreach ($cities as $city) {
                $candidate = $this->normalizeLocationLabel(trim(($city['type'] ?? '') . ' ' . ($city['city_name'] ?? '')));
                $candidateNameOnly = $this->normalizeLocationLabel((string) ($city['city_name'] ?? ''));

                if ($candidate === $normalizedTargetCity || $candidateNameOnly === $normalizedTargetCity) {
                    return (int) ($city['city_id'] ?? 0) ?: null;
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Failed to resolve city ID from legacy address', [
                'address_id' => $address->id,
                'message' => $e->getMessage(),
            ]);
        }

        return null;
    }

    private function resolveProvinceIdByName(string $provinceName): ?int
    {
        try {
            $target = $this->normalizeLocationLabel($provinceName);

            foreach ($this->client->getProvinces() as $province) {
                if ($this->normalizeLocationLabel((string) ($province['province'] ?? '')) === $target) {
                    return (int) ($province['province_id'] ?? 0) ?: null;
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Failed to resolve province ID from legacy address', [
                'province' => $provinceName,
                'message' => $e->getMessage(),
            ]);
        }

        return null;
    }

    private function normalizeLocationLabel(string $value): string
    {
        $value = mb_strtolower(trim($value));

        return preg_replace('/\s+/', ' ', $value) ?? $value;
    }

    /**
     * Fallback shipping estimates when RajaOngkir cannot be reached or mapped.
     */
    private function buildFallbackShippingOptions(Address $address, int $weight): array
    {
        $packages = max(1, (int) ceil($weight / 1000));
        $baseCost = 12000 + ($packages * 2500);

        return [
            [
                'courier_name' => 'jne',
                'service' => 'REG',
                'description' => 'Estimasi pengiriman reguler',
                'cost' => $baseCost,
                'etd' => '2-4',
                'note' => 'Estimasi lokal saat data ongkir tidak tersedia',
            ],
            [
                'courier_name' => 'jnt',
                'service' => 'EZ',
                'description' => 'Estimasi pengiriman ekonomis',
                'cost' => $baseCost + 1500,
                'etd' => '2-4',
                'note' => 'Estimasi lokal saat data ongkir tidak tersedia',
            ],
            [
                'courier_name' => 'sicepat',
                'service' => 'REG',
                'description' => 'Estimasi pengiriman reguler',
                'cost' => $baseCost + 1000,
                'etd' => '1-3',
                'note' => 'Estimasi lokal saat data ongkir tidak tersedia',
            ],
        ];
    }

    private function shouldUseRealShippingOnly(): bool
    {
        return app()->environment('testing');
    }
}
