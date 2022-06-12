<?php

declare(strict_types=1);

namespace SergeevPasha\DPD\DTO;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Attributes\MapFrom;

class TrackingDto extends DataTransferObject
{
    #[MapFrom('clientOrderNr')]
    public string $clientOrderNumber;

    #[MapFrom('dpdOrderNr')]
    public string $trackNumber;

    #[MapFrom('dpdParcelNr')]
    public string $parcelNumber;

    #[MapFrom('pickupDate')]
    public string $pickupDate;

    #[MapFrom('planDeliveryDate')]
    public string $planDeliveryDate;

    #[MapFrom('orderPhysicalWeight')]
    public string $orderPhysicalWeight;

    #[MapFrom('orderVolume')]
    public string $orderVolume;

    #[MapFrom('orderVolumeWeight')]
    public string $orderVolumeWeight;

    #[MapFrom('orderPayWeight')]
    public string $orderPayWeight;

    #[MapFrom('orderCost')]
    public string $orderCost;

    #[MapFrom('parcelPhysicalWeight')]
    public string $parcelPhysicalWeight;

    #[MapFrom('parcelVolume')]
    public string $parcelVolume;

    #[MapFrom('parcelVolumeWeight')]
    public string $parcelVolumeWeight;

    #[MapFrom('parcelPayWeight')]
    public string $parcelPayWeight;

    #[MapFrom('parcelLength')]
    public string $parcelLength;

    #[MapFrom('parcelWidth')]
    public string $parcelWidth;

    #[MapFrom('parcelHeight')]
    public string $parcelHeight;

    #[MapFrom('newState')]
    public string $status;

    #[MapFrom('stateTranslated')]
    public string $translatedStatus;

    #[MapFrom('transitionTime')]
    public string $transitionTime;

    #[MapFrom('terminalCode')]
    public string $terminalCode;

    #[MapFrom('terminalCity')]
    public string $terminalCity;

    #[MapFrom('consignee')]
    public string $consignee;
}
