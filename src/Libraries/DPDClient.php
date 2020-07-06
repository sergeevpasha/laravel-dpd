<?php

declare(strict_types=1);

namespace SergeevPasha\DPD\Libraries;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use SergeevPasha\DPD\DTO\Delivery;
use Psr\Http\Message\ResponseInterface;
use SergeevPasha\DPD\Helpers\DPDHelper;

class DPDClient
{
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
        $this->key = $key;
    }
    
    /**
     * Authorize a User.
     *
     * @param string|null $login
     * @param string|null $password
     *
     * @return string|null
     */
    public function authorize(?string $login = null, ?string $password = null): ?string
    {
        /*
            We need to send a request that will return our DPD Session ID.
            We are not required to send auth data yet
         */
        $response = $this->request('https://www.dpd.ru/ols/order/order.do2', [], null, 'GET');
        $headers = $response->getHeaders();
        $cookies = isset($headers['Set-Cookie']) ? $headers['Set-Cookie'] : [];
        $session = null;
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
            $this->request('https://www.dpd.ru/ols/etc/logon.do2', [
                    'username' => $login ?? config('dpd.login'),
                    'password' => $password ?? config('dpd.password'),
            ], $session);
        }
        return $session;
    }
    
    /**
    * Send request to DPD API.
    *
    * @param string $path
    * @param array<mixed>  $params
    * @param string|null  $session
    * @param string $method
    * @param string  $type
    *
    */
    public function request(
        string $path,
        array $params,
        ?string $session,
        string $method = 'POST',
        string $type = 'form_params'
    ): ResponseInterface {
        $options = [
            $type => $params,
            'http_errors' => false,
        ];
        if ($session) {
            $options['cookies'] = \GuzzleHttp\Cookie\CookieJar::fromArray(['MYDPDSessionID' => $session], 'www.dpd.ru');
        } else {
            $options['cookies'] = new \GuzzleHttp\Cookie\CookieJar();
        }
        $client = new \GuzzleHttp\Client();
        $response = $client->request($method, $path, $options);
        return $response;
    }


    /**
     * Find a city by query string.
     *
     * @param string $query
     * @param string $country
     *
     * @return array<mixed>|null
     */
    public function findCity(string $query, string $country): ?array
    {
        /* Here we get cities without passing a session ID. That's very important */
        $response = $this->request('https://www.dpd.ru/ols/calc/cities.do2', [
            'name_startsWith' => $query,
            'country' => $country,
        ], null);
        return json_decode($response->getBody()->getContents(), true);
    }
    
    /**
     * Find a street by query string and City ID.
     *
     * @param int    $city
     * @param string $query
     * @param string $session
     *
     * @return array<mixed>|null
     */
    public function findCityStreet(int $city, string $query, string $session): ?array
    {
        $response = $this->request('https://www.dpd.ru/ols/order/addressStreetAutocomplete.do2', [
            'cityId' => $city,
            'streetName' => $query,
        ], $session);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Find a Receive Point City
     *
     * @param string $query
     *
     * @return array<mixed>|null
     */
    public function findReceivePointCity(string $query): ?array
    {
        $answer = $this->request('https://chooser.dpd.ru/api/geocode', [
            'value' => $query,
        ], null);
        return json_decode($answer->getBody()->getContents(), true);
    }
    
    /**
     * Find City Receive Points
     *
     * @param string $bounds
     * @param string $city
     *
     * @return array<mixed>|null
     */
    public function getReceivePoints(string $bounds, string $city): ?array
    {
        $answer = $this->request('https://chooser.dpd.ru/api', [
            'bounds' => $bounds,
            'city' => $city,
        ], null, 'query');
        return json_decode($answer->getBody()->getContents(), true);
    }
    
    /**
     * Get City Terminals
     *
     * @param string $bounds
     * @param string $city
     *
     * @return array<mixed>|null
     */
    public function getTerminals(string $bounds, string $city): ?array
    {
        $data = $this->getReceivePoints($bounds, $city);
        $terminals = [];
        if (is_array($data)) {
            $terminals = array_filter(
                $data,
                fn ($array) => $array['workAsPocht'] === 0 && $array['workAsPvp'] === 0 && $array['cityName'] === $city
            );
        }
        return $terminals;
    }
    
    /**
     * Get Delivery Price
     *
     * @param \SergeevPasha\DPD\DTO\Delivery $delivery
     *
     * @return array<mixed>|null
     */
    public function getPrice(Delivery $delivery): ?array
    {
        $soap = new \SoapClient('http://ws.dpd.ru/services/calculator2?wsdl');
        /*
            We gonna subtract 123 to all cities ID, that's the tricky part,
            if we would get cities with Session we would not be able to get
            the true cities ID, as long as they are generated using some of
            its values. Without session we can just subtract 123.
        */
        $data = [
            'auth' => [
                'clientNumber' => $this->user,
                'clientKey'    => $this->key
            ],
            'pickup'           => [
                'cityId'       => $delivery->derivalCityId - 123,
            ],
            'delivery'         => [
                'cityId'       => $delivery->arrivalCityId - 123,
            ],
            'selfPickup'       => $delivery->derivalTerminal,
            'selfDelivery'     => $delivery->arrivalTerminal,
            'weight'           => $delivery->parcelTotalWeight,
            'volume'           => $delivery->parcelTotalVolume,
            'declaredValue'    => $delivery->parcelTotalValue,
            'pickupDate'       => $delivery->pickupDate,
            'maxDays'          => $delivery->maxDeliveryDays,
            'maxPrice'         => $delivery->maxDeliveryPrice,
        ];
        $data = DPDHelper::removeNullValues($data);
        $request['request'] = $data;
        /* @phpstan-ignore-next-line */
        $result = $soap->getServiceCost2($request);
        return (array) $result;
    }
}
