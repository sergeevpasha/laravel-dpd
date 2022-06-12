<?php

declare(strict_types=1);

namespace SergeevPasha\DPD\DTO;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Attributes\MapFrom;

class CityDto extends DataTransferObject
{
    #[MapFrom('cityName')]
    public string $name;

    #[MapFrom('cityCode')]
    public string $code;

    #[MapFrom('cityId')]
    public string $cityId;

    #[MapFrom('countryCode')]
    public string $countryCode;

    #[MapFrom('regionCode')]
    public string $regionCode;

    #[MapFrom('regionName')]
    public string $regionName;

    #[MapFrom('abbreviation')]
    public string $abbreviation;

    #[MapFrom('indexMin')]
    public string|null $indexMin;

    #[MapFrom('indexMax')]
    public string|null $indexMax;
}
