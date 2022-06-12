<?php

declare(strict_types=1);

namespace SergeevPasha\DPD\Libraries;

use SergeevPasha\DPD\DTO\CityDto;
use SergeevPasha\DPD\DTO\DeliveryDto;
use SergeevPasha\DPD\DTO\TerminalDto;
use SergeevPasha\DPD\DTO\TrackingDto;
use SergeevPasha\DPD\Helpers\DPDHelper;
use SergeevPasha\DPD\DTO\DeliveryPriceOptionDto;
use SergeevPasha\DPD\DTO\Collections\CityDtoCollection;
use SergeevPasha\DPD\DTO\Collections\TerminalDtoCollection;
use SergeevPasha\DPD\DTO\Collections\DeliveryPriceOptionDtoCollection;

class DPDClient
{
    public const PRODUCTION_URL = 'https://ws.dpd.ru/';
    public const TEST_URL       = 'https://wstest.dpd.ru/';

    private string $user;
    private string $key;
    private string $url;

    public function __construct(string $user, string $key, bool $testing = false)
    {
        $this->user = $user;
        $this->key  = $key;
        $this->url  = $testing ? self::TEST_URL : self::PRODUCTION_URL;
    }

    /**
     * @param string $country
     *
     * @throws \SoapFault
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     * @return \SergeevPasha\DPD\DTO\Collections\CityDtoCollection
     */
    public function getCountryCities(string $country): CityDtoCollection
    {
        $soap = new SoapService($this->url . 'services/geography2?wsdl');

        $data   = [
            'auth'        => [
                'clientNumber' => $this->user,
                'clientKey'    => $this->key
            ],
            'countryCode' => $country
        ];
        $result = $soap->getCitiesCashPay(['request' => $data]);
        $cities = !isset($result->return[0]) ? [] : $result->return;
        return new CityDtoCollection(array_map(fn($city) => new CityDto((array) $city), $cities));
    }

    /**
     * @throws \SoapFault
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     * @return \SergeevPasha\DPD\DTO\Collections\TerminalDtoCollection
     */
    public function getTerminals(): TerminalDtoCollection
    {
        $soap = new SoapService($this->url . 'services/geography2?wsdl');

        $request   = [
            'auth' => [
                'clientNumber' => $this->user,
                'clientKey'    => $this->key
            ]
        ];
        $result    = $soap->getTerminalsSelfDelivery2($request);
        $terminals = !isset($result->return->terminal) ? [] : $result->return->terminal;
        return new TerminalDtoCollection(
            array_map(
                fn($terminal) => new TerminalDto(json_decode(json_encode($terminal), true)),
                $terminals
            )
        );
    }

    /**
     * @param \SergeevPasha\DPD\DTO\DeliveryDto $delivery
     *
     * @throws \SoapFault
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     * @return \SergeevPasha\DPD\DTO\Collections\DeliveryPriceOptionDtoCollection
     */
    public function getPrice(DeliveryDto $delivery): DeliveryPriceOptionDtoCollection
    {
        $soap               = new SoapService($this->url . 'services/calculator2?wsdl');
        $data               = [
            'auth'          => [
                'clientNumber' => $this->user,
                'clientKey'    => $this->key
            ],
            'pickup'        => [
                'cityId' => $delivery->derivalCityId,
            ],
            'delivery'      => [
                'cityId' => $delivery->arrivalCityId,
            ],
            'selfPickup'    => $delivery->derivalTerminal,
            'selfDelivery'  => $delivery->arrivalTerminal,
            'weight'        => $delivery->parcelTotalWeight,
            'volume'        => $delivery->parcelTotalVolume,
            'declaredValue' => $delivery->parcelTotalValue,
            'pickupDate'    => $delivery->pickupDate,
            'maxDays'       => $delivery->maxDeliveryDays,
            'maxPrice'      => $delivery->maxDeliveryPrice,
        ];
        $data               = DPDHelper::removeNullValues($data);
        $request['request'] = $data;
        $result             = $soap->getServiceCost2($request);
        $options            = !isset($result->return) ? [] : $result->return;

        return new DeliveryPriceOptionDtoCollection(
            array_map(
                fn($option) => new DeliveryPriceOptionDto(json_decode(json_encode($option), true)),
                $options
            )
        );
    }

    /**
     * @param string $trackNumber
     *
     * @throws \SoapFault
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     * @return \SergeevPasha\DPD\DTO\TrackingDto
     */
    public function findByTrackNumber(string $trackNumber): TrackingDto
    {
        $soap                    = new SoapService($this->url . 'services/tracing1-1?wsdl');
        $data                    = [
            'auth'       => [
                'clientNumber' => $this->user,
                'clientKey'    => $this->key
            ],
            'dpdOrderNr' => $trackNumber
        ];
        $states                  = $soap->getStatesByDPDOrder(['request' => $data]);
        $result                  = !isset($states->return->states) ? [] : end($states->return->states);
        $result->stateTranslated = trans("dpd::dpd_statuses.$result->newState");

        return new TrackingDto((array) $result);
    }
}
