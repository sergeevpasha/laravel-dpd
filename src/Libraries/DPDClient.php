<?php

declare(strict_types=1);

namespace SergeevPasha\DPD\Libraries;

use Exception;
use SoapClient;
use GuzzleHttp\Cookie\CookieJar;
use SergeevPasha\DPD\DTO\Delivery;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;
use SergeevPasha\DPD\Helpers\DPDHelper;

class DPDClient
{
    /**
     * Value to add to the result city ID
     *
     * @var int
     */
    private int $currentMagicValue;

    /**
     * DPD User.
     *
     * @var string
     */
    private string $user;

    /**
     * DPD App key.
     *
     * @var string
     */
    private string $key;

    public function __construct(string $user, string $key)
    {
        $this->user = $user;
        $this->key  = $key;
    }

    /**
     * Authorize a User.
     *
     * @param string|null $login
     * @param string|null $password
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return string|null
     */
    public function authorize(?string $login = null, ?string $password = null): ?string
    {
        /*
            We need to send a request that will return our DPD Session ID.
            We are not required to send auth data yet
         */
        $response = $this->request('https://www.dpd.ru/ols/order/order.do2', [], null, 'GET');
        $headers  = $response->getHeaders();
        $cookies  = isset($headers['Set-Cookie']) ? $headers['Set-Cookie'] : [];
        $session  = null;
        foreach ($cookies as $cookie) {
            $basicChunks = explode(';', $cookie);
            foreach ($basicChunks as $basicChunk) {
                $generalChunks = explode('=', $basicChunk);
                if ($generalChunks[0] === 'MYDPDSessionID') {
                    $session = $generalChunks[1];
                }
            }
        }
        if ($session) {
            /* That's the tricky part, if we have our session we are now able to login with our credentials */
            $this->request(
                'https://www.dpd.ru/ols/etc/logon.do2',
                [
                    'username' => $login ?? config('dpd.login'),
                    'password' => $password ?? config('dpd.password'),
                ],
                $session
            );
        }
        return $session;
    }

    /**
     * Find Magic value
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function findMagicValue(): void
    {
        $realCityId    = 48994107;
        $currentCities = $this->findCity('Екатеринбург', '3');
        if ($currentCities) {
            $this->currentMagicValue = $realCityId - $currentCities['geonames'][0]['id'];
        } else {
            throw new Exception('Failed to connect to DPD Server');
        }
    }

    /**
     * Send request to DPD API.
     *
     * @param string       $path
     * @param array<mixed> $params
     * @param string|null  $session
     * @param string       $method
     * @param string       $type
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function request(
        string $path,
        array $params,
        ?string $session,
        string $method = 'POST',
        string $type = 'form_params'
    ): ResponseInterface {
        $options = [
            $type         => $params,
            'http_errors' => false,
        ];
        if ($session) {
            $options['cookies'] = CookieJar::fromArray(['MYDPDSessionID' => $session], 'www.dpd.ru');
        } else {
            $options['cookies'] = new CookieJar();
        }
        $client = new GuzzleClient();
        return $client->request($method, $path, $options);
    }


    /**
     * Find a city by query string.
     *
     * @param string $query
     * @param string $country
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return array<mixed>|null
     */
    public function findCity(string $query, string $country): ?array
    {
        /* Here we get cities without passing a session ID. That's very important */
        $response = $this->request(
            'https://www.dpd.ru/ols/calc/cities.do2',
            [
                'name_startsWith' => $query,
                'country'         => $country,
            ],
            null
        );
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Find a street by query string and City ID.
     *
     * @param int    $city
     * @param string $query
     * @param string $session
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return array<mixed>|null
     */
    public function findCityStreet(int $city, string $query, string $session): ?array
    {
        $this->findMagicValue();
        $response = $this->request(
            'https://www.dpd.ru/ols/order/addressStreetAutocomplete.do2',
            [
                'cityId'     => $city + $this->currentMagicValue,
                'streetName' => $query,
            ],
            $session
        );
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Find a Receive Point City
     *
     * @param string $query
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return array<mixed>|null
     */
    public function findReceivePointCity(string $query): ?array
    {
        $answer = $this->request(
            'https://chooser.dpd.ru/api/geocode',
            [
                'value' => $query,
            ],
            null
        );
        return json_decode($answer->getBody()->getContents(), true);
    }

    /**
     * Find City Receive Points
     *
     * @param string $bounds
     * @param string $city
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return array<mixed>|null
     */
    public function getReceivePoints(string $bounds, string $city): ?array
    {
        $answer = $this->request(
            'https://chooser.dpd.ru/api',
            [
                'bounds' => $bounds,
                'city'   => $city,
            ],
            null,
            'query'
        );
        return json_decode($answer->getBody()->getContents(), true);
    }

    /**
     * Get City Terminals
     *
     * @param string $bounds
     * @param string $city
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return array<mixed>|null
     */
    public function getTerminals(string $bounds, string $city): ?array
    {
        $data      = $this->getReceivePoints($bounds, $city);
        $terminals = [];
        if (is_array($data)) {
            $terminals = array_filter(
                $data,
                fn($array) => $array['workAsPocht'] === 0 && $array['workAsPvp'] === 0 && $array['cityName'] === $city
            );
        }
        return $terminals;
    }

    /**
     * Get Delivery Price
     *
     * @param \SergeevPasha\DPD\DTO\Delivery $delivery
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return array<mixed>|null
     */
    public function getPrice(Delivery $delivery): ?array
    {
        $this->findMagicValue();
        $soap = new SoapClient('http://ws.dpd.ru/services/calculator2?wsdl');
        /*
            We gonna add magic value to all cities ID, that's the tricky part,
            if we would get cities with Session we would not be able to get
            the true cities ID, as long as they are generated using some of
            its values. Without session we can just subtract magic value.
        */
        $data               = [
            'auth'          => [
                'clientNumber' => $this->user,
                'clientKey'    => $this->key
            ],
            'pickup'        => [
                'cityId' => $delivery->derivalCityId + $this->currentMagicValue,
            ],
            'delivery'      => [
                'cityId' => $delivery->arrivalCityId + $this->currentMagicValue,
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
        /* @phpstan-ignore-next-line */
        $result = $soap->getServiceCost2($request);
        return (array) $result;
    }
}
