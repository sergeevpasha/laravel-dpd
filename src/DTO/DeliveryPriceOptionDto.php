<?php

namespace SergeevPasha\DPD\DTO;

use SergeevPasha\DPD\Enum\ServiceType;
use Spatie\DataTransferObject\DataTransferObject;

class DeliveryPriceOptionDto extends DataTransferObject
{
    /**
     * @var \SergeevPasha\DPD\Enum\ServiceType
     */
    public ServiceType $service;

    /**
     * @var float
     */
    public float $cost;

    /**
     * @var int
     */
    public int $days;

    /**
     * From Array.
     *
     * @param array $data
     *
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumKeyException
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            [
                'service' => ServiceType::fromKey($data['serviceCode']),
                'cost'    => (float) $data['cost'],
                'days'    => (int) $data['days'],
            ]
        );
    }
}
