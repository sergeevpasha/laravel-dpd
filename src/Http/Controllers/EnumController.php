<?php

declare(strict_types=1);

namespace SergeevPasha\DPD\Http\Controllers;

use Illuminate\Http\JsonResponse;
use SergeevPasha\DPD\Enum\CountryType;
use SergeevPasha\DPD\Enum\ServiceType;

class EnumController
{
    /**
     * Get all Countries.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function countries(): JsonResponse
    {
        return response()->json(CountryType::toArray());
    }

    /**
     * Get all available services.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function services(): JsonResponse
    {
        return response()->json(ServiceType::toArray());
    }
}
