<?php

declare(strict_types=1);

namespace SergeevPasha\DPD\Tests\Feature\Controllers;

use Illuminate\Http\JsonResponse;
use SergeevPasha\DPD\Tests\TestCase;
use SergeevPasha\DPD\Enum\CountryType;
use SergeevPasha\DPD\Enum\ServiceType;
use SergeevPasha\DPD\Http\Controllers\EnumController;

class EnumControllerTest extends TestCase
{
    /**
     * Controller.
     *
     * @var \SergeevPasha\DPD\Http\Controllers\EnumController
     */
    protected EnumController $controller;

    /**
     * Set Up requirements.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = $this->app->make(EnumController::class);
    }

    public function testCountries()
    {
        $method = $this->controller->countries();
        $this->assertInstanceOf(JsonResponse::class, $method);
        $this->assertEqualsCanonicalizing(
            json_encode(CountryType::asArray()),
            $method->content()
        );
    }

    public function testServices()
    {
        $method = $this->controller->services();
        $this->assertInstanceOf(JsonResponse::class, $method);
        $this->assertEqualsCanonicalizing(
            json_encode(ServiceType::asArray()),
            $method->content()
        );
    }
}
