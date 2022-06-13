<?php

namespace SergeevPasha\DPD\DTO;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Attributes\MapFrom;

class DeliveryPriceOptionDto extends DataTransferObject
{

    #[MapFrom('serviceCode')]
    public string $serviceCode;

    #[MapFrom('serviceName')]
    public string $serviceName;

    #[MapFrom('cost')]
    public string $cost;

    #[MapFrom('days')]
    public string $days;
}
