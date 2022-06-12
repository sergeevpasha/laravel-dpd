<?php

declare(strict_types=1);

namespace SergeevPasha\DPD\DTO\Collections;

use Illuminate\Support\Collection;
use SergeevPasha\DPD\DTO\DeliveryPriceOptionDto;

class DeliveryPriceOptionDtoCollection extends Collection
{
    public function offsetGet($key): DeliveryPriceOptionDto
    {
        return parent::offsetGet($key);
    }
}
