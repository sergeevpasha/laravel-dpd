<?php

declare(strict_types=1);

namespace SergeevPasha\DPD\Tests\Feature\Controllers;

use Mockery;
use Illuminate\Http\JsonResponse;
use SergeevPasha\DPD\Tests\TestCase;
use SergeevPasha\DPD\Libraries\DPDClient;
use Illuminate\Validation\ValidationException;
use SergeevPasha\DPD\Http\Controllers\AuthDPDController;

class AuthDPDControllerTest extends TestCase
{
    public function testInvokeAuthorization()
    {
        $client = Mockery::mock(DPDClient::class);
        $expectedData = 'session';
        $client->shouldReceive('authorize')->andReturn($expectedData);
        $this->app->instance(DPDClient::class, $client);
        $class = $this->app->make(AuthDPDController::class);
        $method = $class->__invoke();
        $this->assertInstanceOf(JsonResponse::class, $method);
    }
    
    public function testInvokeAuthorizationFails()
    {
        $client = Mockery::mock(DPDClient::class);
        $expectedData = null;
        $client->shouldReceive('authorize')->andReturn($expectedData);
        $this->app->instance(DPDClient::class, $client);
        $class = $this->app->make(AuthDPDController::class);
        $this->expectException(ValidationException::class);
        $class->__invoke();
    }
}
