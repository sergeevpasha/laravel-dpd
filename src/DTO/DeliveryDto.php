<?php

declare(strict_types=1);

namespace SergeevPasha\DPD\DTO;

use Illuminate\Support\Str;
use SergeevPasha\DPD\Enum\ServiceType;
use Spatie\DataTransferObject\DataTransferObject;

class DeliveryDto extends DataTransferObject
{
    /**
     * @var int
     */
    public int $arrivalCityId;

    /**
     * @var int
     */
    public int $derivalCityId;

    /**
     * @var bool
     */
    public bool $arrivalTerminal;

    /**
     * @var bool
     */
    public bool $derivalTerminal;

    /**
     * @var float
     */
    public float $parcelTotalWeight;

    /**
     * @var float|null
     */
    public ?float $parcelTotalVolume;

    /**
     * @var float|null
     */
    public ?float $parcelTotalValue;

    /**
     * @var string|null
     */
    public ?string $pickupDate;

    /**
     * @var int|null
     */
    public ?int $maxDeliveryDays;

    /**
     * @var float|null
     */
    public ?float $maxDeliveryPrice;

    /**
     * @var \SergeevPasha\DPD\Enum\ServiceType[]|null
     */
    public ?array $services;

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
                'arrivalCityId'     => (int) $data['arrival_city_id'],
                'derivalCityId'     => (int) $data['derival_city_id'],
                'arrivalTerminal'   => (bool) $data['arrival_terminal'],
                'derivalTerminal'   => (bool) $data['derival_terminal'],
                'parcelTotalWeight' => (float) $data['parcel_total_weight'],
                'parcelTotalVolume' => isset($data['parcel_total_volume']) ?
                    (float) $data['parcel_total_volume'] : null,
                'parcelTotalValue'  => isset($data['parcel_total_value']) ?
                    (float) $data['parcel_total_value'] : null,
                'pickupDate'        => $data['pickup_date'] ?? null,
                'maxDeliveryDays'   => isset($data['max_delivery_days']) ? (int) $data['max_delivery_days'] : null,
                'maxDeliveryPrice'  => isset($data['max_delivery_price']) ? (float) $data['max_delivery_price'] : null,
                'services'          =>
                    isset($data['services']) ?
                        array_map(fn(string $item) => ServiceType::fromKey(Str::upper($item)), $data['services']) :
                        null,
            ]
        );
    }
}
