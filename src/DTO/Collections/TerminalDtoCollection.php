<?php

declare(strict_types=1);

namespace SergeevPasha\DPD\DTO\Collections;

use Illuminate\Support\Collection;
use SergeevPasha\DPD\DTO\TerminalDto;

class TerminalDtoCollection extends Collection
{
    public function offsetGet($key): TerminalDto
    {
        return parent::offsetGet($key);
    }
}
