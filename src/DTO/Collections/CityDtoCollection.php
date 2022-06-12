<?php

declare(strict_types=1);

namespace SergeevPasha\DPD\DTO\Collections;

use SergeevPasha\DPD\DTO\CityDto;
use Illuminate\Support\Collection;

class CityDtoCollection extends Collection
{
    public function offsetGet($key): CityDto
    {
        return parent::offsetGet($key);
    }
}
