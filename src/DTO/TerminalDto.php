<?php

declare(strict_types=1);

namespace SergeevPasha\DPD\DTO;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Attributes\MapFrom;

class TerminalDto extends DataTransferObject
{
    #[MapFrom('terminalName')]
    public string $name;

    #[MapFrom('terminalCode')]
    public string $code;

    #[MapFrom('address.countryCode')]
    public string $countryCode;

    #[MapFrom('address.regionCode')]
    public string $regionCode;

    #[MapFrom('address.regionName')]
    public string $regionName;

    #[MapFrom('address.cityId')]
    public string $cityId;

    #[MapFrom('address.cityCode')]
    public string $cityCode;

    #[MapFrom('address.cityName')]
    public string $cityName;

    #[MapFrom('address.index')]
    public string $index;

    #[MapFrom('address.street')]
    public string|null $street;

    #[MapFrom('address.streetAbbr')]
    public string|null $streetAbbr;

    #[MapFrom('address.houseNo')]
    public string|null $house;

    #[MapFrom('address.structure')]
    public string|null $structure;

    #[MapFrom('address.ownership')]
    public string|null $ownership;

    #[MapFrom('address.descript')]
    public string|null $description;

    #[MapFrom('geoCoordinates.latitude')]
    public float|null $latitude;

    #[MapFrom('geoCoordinates.longitude')]
    public float|null $longitude;
}
