<?php

namespace Tests\Unit;

use App\Http\Controllers\Customer\RajaOngkirController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RajaOngkirControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    public function test_postal_codes_endpoint_normalizes_district_id_and_returns_options(): void
    {
        Http::fake([
            'https://raw.githubusercontent.com/laravolt/indonesia/master/resources/csv/villages/11.csv' => Http::response(
                "1101012001,110101,KEUDE BAKONGAN,2.931094803160483,97.48458404258515,23773\n" .
                "1101012002,110101,UJONG MANGKI,2.9527245335971086,97.43761867741745,23773\n",
                200
            ),
        ]);

        $controller = app(RajaOngkirController::class);
        $request = Request::create('/', 'GET', ['district_id' => '1101010']);

        $response = $controller->postalCodes($request);
        $payload = $response->getData(true);

        $this->assertTrue($payload['success']);
        $this->assertSame(['23773'], $payload['data']);
        Storage::disk('local')->assertExists('region-cache/postal-codes_110101.json');
    }

    public function test_provinces_endpoint_writes_local_cache_file(): void
    {
        Http::fake([
            'https://raw.githubusercontent.com/emsifa/api-wilayah-indonesia/master/static/api/provinces.json' => Http::response(
                [
                    ['id' => '11', 'name' => 'ACEH'],
                    ['id' => '12', 'name' => 'SUMATERA UTARA'],
                ],
                200
            ),
        ]);

        $controller = app(RajaOngkirController::class);

        $response = $controller->provinces();
        $payload = $response->getData(true);

        $this->assertTrue($payload['success']);
        $this->assertCount(2, $payload['data']);
        Storage::disk('local')->assertExists('region-cache/static_api_provinces.json');
    }
}