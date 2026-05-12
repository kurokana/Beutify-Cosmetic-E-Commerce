<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class RajaOngkirController extends Controller
{
    private const REGION_REPO = 'emsifa/api-wilayah-indonesia';
    private const REGION_REF = 'master';
    private const LARAVOLT_REPO = 'laravolt/indonesia';
    private const LARAVOLT_REF = 'master';
    private const CACHE_DISK = 'local';
    private const CACHE_ROOT = 'region-cache';

    public function provinces(): JsonResponse
    {
        try {
            $provinces = array_map(fn (array $province) => [
                'province_id' => $province['id'] ?? '',
                'province'    => $province['name'] ?? '',
            ], $this->getRegionJson('static/api/provinces.json'));
            return response()->json(['success' => true, 'data' => $provinces]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function cities(Request $request): JsonResponse
    {
        $request->validate(['province_id' => ['required']]);

        try {
            $cities = array_map(fn (array $city) => $this->transformCity($city), $this->getRegionJson("static/api/regencies/{$request->province_id}.json"));
            return response()->json(['success' => true, 'data' => $cities]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function subdistricts(Request $request): JsonResponse
    {
        $request->validate(['city_id' => ['required']]);

        try {
            $subs = array_map(fn (array $district) => [
                'subdistrict_id'   => $district['id'] ?? '',
                'city_id'          => $district['regency_id'] ?? $request->city_id,
                'subdistrict_name'  => $district['name'] ?? '',
                'postal_code'      => '',
            ], $this->getRegionJson("static/api/districts/{$request->city_id}.json"));
            return response()->json(['success' => true, 'data' => $subs]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function postalCodes(Request $request): JsonResponse
    {
        $request->validate(['district_id' => ['required']]);

        try {
            $postalCodes = $this->getPostalCodesForDistrict((string) $request->district_id);

            return response()->json(['success' => true, 'data' => $postalCodes]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Load region JSON from the public static API repo.
     */
    private function getRegionJson(string $path): array
    {
        $cachePath = $this->cacheFilePath($path, 'json');
        $cached = $this->readCachedFile($cachePath);

        if ($cached !== null) {
            $json = json_decode($cached, true);
            if (is_array($json)) {
                return $json;
            }
        }

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'User-Agent' => 'KosmetikEcommerce/1.0',
        ])->get($this->rawRegionUrl($path));

        if ($response->failed()) {
            throw new \Exception('Gagal memuat data wilayah.');
        }

        $json = $response->json();
        if (! is_array($json)) {
            throw new \Exception('Format data wilayah tidak valid.');
        }

        $this->writeCachedFile($cachePath, json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $json;
    }

    private function transformCity(array $city): array
    {
        $name = strtoupper(trim((string) ($city['name'] ?? '')));
        $type = 'Kabupaten';
        $cityName = $name;

        if (str_starts_with($name, 'KOTA ')) {
            $type = 'Kota';
            $cityName = trim(substr($name, 5));
        } elseif (str_starts_with($name, 'KABUPATEN ')) {
            $type = 'Kabupaten';
            $cityName = trim(substr($name, 10));
        }

        return [
            'city_id'   => $city['id'] ?? '',
            'province_id' => $city['province_id'] ?? '',
            'type'      => $type,
            'city_name' => $cityName,
        ];
    }

    /**
     * Get unique postal codes for a district from the villages CSV of a province.
     */
    private function getPostalCodesForDistrict(string $districtId): array
    {
        $districtCode = $this->normalizeDistrictCode($districtId);
        $cacheKey = 'region:postal_codes:' . $districtCode;

        return Cache::remember($cacheKey, now()->addDay(), function () use ($districtCode) {
            $cachePath = $this->cacheFilePath("postal-codes/{$districtCode}", 'json');
            $cached = $this->readCachedFile($cachePath);
            if ($cached !== null) {
                $decoded = json_decode($cached, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }

            $provinceCode = substr($districtCode, 0, 2);
            $csv = $this->getRegionCsv("resources/csv/villages/{$provinceCode}.csv");

            $lines = preg_split('/\r\n|\r|\n/', trim($csv)) ?: [];
            if (count($lines) < 2) {
                return [];
            }

            $postalCodes = [];

            for ($index = 1; $index < count($lines); $index++) {
                $row = str_getcsv($lines[$index]);

                if (count($row) < 6) {
                    continue;
                }

                [$code, $rowDistrictId, $name, $lat, $long, $pos] = $row;

                if ((string) $rowDistrictId !== $districtCode) {
                    continue;
                }

                $pos = trim((string) $pos);
                if ($pos === '') {
                    continue;
                }

                $postalCodes[$pos] = $pos;
            }

            ksort($postalCodes, SORT_NATURAL);

            $postalCodes = array_values($postalCodes);
            $this->writeCachedFile($cachePath, json_encode($postalCodes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return $postalCodes;
        });
    }

    private function normalizeDistrictCode(string $districtId): string
    {
        $districtId = preg_replace('/\D+/', '', $districtId) ?? $districtId;

        if (strlen($districtId) > 6) {
            return substr($districtId, 0, 6);
        }

        return $districtId;
    }

    private function getRegionCsv(string $path): string
    {
        $cachePath = $this->cacheFilePath($path, 'csv');
        $cached = $this->readCachedFile($cachePath);

        if ($cached !== null) {
            return $cached;
        }

        $response = Http::withHeaders([
            'Accept' => 'text/plain',
            'User-Agent' => 'KosmetikEcommerce/1.0',
        ])->get($this->rawLaravoltUrl($path));

        if ($response->failed()) {
            throw new \Exception('Gagal memuat data kodepos.');
        }

        $body = (string) $response->body();
        if (trim($body) === '') {
            throw new \Exception('Data kodepos tidak tersedia.');
        }

        $this->writeCachedFile($cachePath, $body);

        return $body;
    }

    private function rawRegionUrl(string $path): string
    {
        return 'https://raw.githubusercontent.com/' . self::REGION_REPO . '/' . self::REGION_REF . '/' . ltrim($path, '/');
    }

    private function rawLaravoltUrl(string $path): string
    {
        return 'https://raw.githubusercontent.com/' . self::LARAVOLT_REPO . '/' . self::LARAVOLT_REF . '/' . ltrim($path, '/');
    }

    private function cacheFilePath(string $key, string $extension): string
    {
        $safeKey = preg_replace('/\.(json|csv)$/i', '', $key) ?? $key;
        $safeKey = str_replace(['\\', '/', ':'], '_', $safeKey);

        return self::CACHE_ROOT . '/' . $safeKey . '.' . $extension;
    }

    private function readCachedFile(string $path): ?string
    {
        if (! Storage::disk(self::CACHE_DISK)->exists($path)) {
            return null;
        }

        $contents = Storage::disk(self::CACHE_DISK)->get($path);

        return trim((string) $contents) === '' ? null : $contents;
    }

    private function writeCachedFile(string $path, string $contents): void
    {
        Storage::disk(self::CACHE_DISK)->put($path, $contents);
    }
}
