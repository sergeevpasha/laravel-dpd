<?php

declare(strict_types=1);

namespace SergeevPasha\DPD\Tests\Unit;

use SergeevPasha\DPD\Tests\TestCase;
use SergeevPasha\DPD\Providers\DPDServiceProvider;

class ServiceProviderTest extends TestCase
{
    public function testServiceProvider(): void
    {
        $service = new DPDServiceProvider($this->app);
        $this->assertNull($service->register());
        $this->assertNull($service->boot());
    }
}
