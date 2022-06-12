<?php

declare(strict_types=1);

namespace SergeevPasha\DPD\DTO\Collections;

use Illuminate\Support\Collection;
use SergeevPasha\DPD\DTO\TrackingDto;

class TrackingDtoCollection extends Collection
{
    public function offsetGet($key): TrackingDto
    {
        return parent::offsetGet($key);
    }
}
