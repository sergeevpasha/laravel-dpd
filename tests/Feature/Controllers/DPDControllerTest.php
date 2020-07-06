<?php

declare(strict_types=1);

namespace SergeevPasha\DPD\Tests\Feature\Controllers;

use Mockery;
use Exception;
use Illuminate\Http\JsonResponse;
use SergeevPasha\DPD\Tests\TestCase;
use SergeevPasha\DPD\Libraries\DPDClient;
use SergeevPasha\DPD\Http\Controllers\DPDController;
use SergeevPasha\DPD\Http\Requests\DPDTerminalRequest;
use SergeevPasha\DPD\Http\Requests\DPDQueryCityRequest;
use SergeevPasha\DPD\Http\Requests\DPDQueryStreetRequest;
use SergeevPasha\DPD\Http\Requests\DPDCalculatePriceRequest;
use SergeevPasha\DPD\Http\Requests\DPDQueryReceivePointsRequest;
use SergeevPasha\DPD\Http\Requests\DPDQueryReceivePointCityRequest;

class DPDControllerTest extends TestCase
{
    /**
     * Default Response.
     *
     * @var array
     */
    protected array $defaultResponse = [
        'data' => 'text',
        'geonames' => 'text',
        'streetList' => 'text',
        'return' => 'text',
    ];

    /**
     * Controller.
     *
     * @var \SergeevPasha\DPD\Http\Controllers\DPDController
     */
    protected DPDController $controller;

    /**
     * Set Up requirements.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $client = Mockery::mock(DPDClient::class);
        $client->shouldReceive('findCity')->andReturn($this->defaultResponse);
        $client->shouldReceive('findCityStreet')->andReturn($this->defaultResponse);
        $client->shouldReceive('findReceivePointCity')->andReturn($this->defaultResponse);
        $client->shouldReceive('getReceivePoints')->andReturn($this->defaultResponse);
        $client->shouldReceive('getTerminals')->andReturn($this->defaultResponse);
        $client->shouldReceive('getPrice')->andReturn($this->defaultResponse);
        $this->app->instance(DPDClient::class, $client);
        $this->controller = $this->app->make(DPDController::class);
    }

    public function testQueryCity()
    {
        $request = new DPDQueryCityRequest([
            'query'        => 'string',
            'country_code' => 'string',
        ]);
        $method = $this->controller->queryCity($request);
        $this->assertInstanceOf(JsonResponse::class, $method);
    }

    public function testResponseOrFail()
    {
        $request = [
            'value' => 'text',
        ];
        $expected = [
            'data' => 'text'
        ];
        $result = $this->controller->responseOrFail($request, 'value');
        $this->assertEqualsCanonicalizing($expected, $result);
        $this->expectException(Exception::class);
        $this->controller->responseOrFail([], 'text');
    }

    public function testQueryStreet()
    {
        $request = new DPDQueryStreetRequest([
            'session_id' => 'string',
            'query'      => 'string',
        ]);
        $method = $this->controller->queryStreet(1, $request);
        $this->assertInstanceOf(JsonResponse::class, $method);
    }

    public function testQueryReceivePointCity()
    {
        $request = new DPDQueryReceivePointCityRequest([
            'query' => 'string',
        ]);
        $method = $this->controller->queryReceivePointCity($request);
        $this->assertInstanceOf(JsonResponse::class, $method);
    }

    public function testGetReceivePoints()
    {
        $request = new DPDQueryReceivePointsRequest([
            'bounds' => 'string',
            'city'   => 'string',
        ]);
        $method = $this->controller->getReceivePoints($request);
        $this->assertInstanceOf(JsonResponse::class, $method);
    }

    public function testGetTerminals()
    {
        $request = new DPDTerminalRequest([
            'bounds' => 'string',
            'city'   => 'string',
        ]);
        $method = $this->controller->getTerminals($request);
        $this->assertInstanceOf(JsonResponse::class, $method);
    }

    public function testCalculateDeliveryPrice()
    {
        $request = new DPDCalculatePriceRequest([
            'arrival_city_id'     => 'string',
            'derival_city_id'     => 'string',
            'arrival_terminal'    => '1',
            'derival_terminal'    => '0',
            'parcel_total_weight' => '10',
            'parcel_total_volume' => '0.1',
            'parcel_total_value'  => '100',
            'services'            => [
                'BZP'
            ],
            'pickup_date'         => 'string',
            'max_delivery_days'   => 'string',
            'max_delivery_price'  => 'string',
        ]);
        $method = $this->controller->calculateDeliveryPrice($request);
        $this->assertInstanceOf(JsonResponse::class, $method);
    }
}
