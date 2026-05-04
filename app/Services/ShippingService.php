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
        // For now, we'll use a simple city ID extraction from the address
        // In a real implementation, you'd need to map city names to RajaOngkir city IDs
        // or store the city_id in the addresses table
        $destinationCityId = $this->getCityIdFromAddress($address);

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
            throw new \Exception('Tidak dapat menghitung ongkos kirim. Silakan coba lagi.');
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
        // TODO: Implement proper city ID mapping
        // For now, we'll assume the city field contains a numeric city ID
        // or we'll use a default value for testing

        if (is_numeric($address->city)) {
            return (int) $address->city;
        }

        // If city is not numeric, we need to map it to a RajaOngkir city ID
        // This would typically involve calling RajaOngkir's city API or using a local mapping
        throw new \Exception('Kota tujuan tidak valid. Silakan pilih kota dari daftar yang tersedia.');
    }
}
