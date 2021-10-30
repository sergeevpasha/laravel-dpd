<?php

/** @noinspection PhpPossiblePolymorphicInvocationInspection */

declare(strict_types=1);

namespace SergeevPasha\DPD\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use SergeevPasha\DPD\DTO\Delivery;
use SergeevPasha\DPD\Libraries\DPDClient;
use Illuminate\Validation\ValidationException;
use SergeevPasha\DPD\Http\Requests\DPDTerminalRequest;
use SergeevPasha\DPD\Http\Requests\DPDQueryCityRequest;
use SergeevPasha\DPD\Http\Requests\DPDQueryStreetRequest;
use SergeevPasha\DPD\Http\Requests\DPDCalculatePriceRequest;
use SergeevPasha\DPD\Http\Requests\DPDFindByTrackNumberRequest;
use SergeevPasha\DPD\Http\Requests\DPDQueryReceivePointsRequest;
use SergeevPasha\DPD\Http\Requests\DPDQueryReceivePointCityRequest;

class DPDController
{
    /**
     * DPD Client Instance.
     *
     * @var \SergeevPasha\DPD\Libraries\DPDClient
     */
    private DPDClient $client;

    public function __construct(DPDClient $client)
    {
        $this->client = $client;
    }

    /**
     * Check if required key is isset and fail if not
     *
     * @param array|null  $data
     * @param string|null $key
     *
     * @throws \Exception
     * @return array
     */
    public function responseOrFail(?array $data, string $key = null): array
    {
        $response = [];
        if ($key) {
            if (!isset($data[$key])) {
                throw new Exception('Missing required parameters or session ID is expired');
            }
            $response['data'] = $data[$key];
        } else {
            foreach ($data as $row) {
                $response['data'][] = $row;
            }
        }
        return $response;
    }

    /**
     * Query City.
     *
     * @param \SergeevPasha\DPD\Http\Requests\DPDQueryCityRequest $request
     *
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return \Illuminate\Http\JsonResponse
     */
    public function queryCity(DPDQueryCityRequest $request): JsonResponse
    {
        $data     = $this->client->findCity($request->query('query'), $request->query('country_code'));
        $response = $this->responseOrFail($data, 'geonames');
        return response()->json($response);
    }

    /**
     * Query Street.
     *
     * @param int                                                   $city
     * @param \SergeevPasha\DPD\Http\Requests\DPDQueryStreetRequest $request
     *
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return \Illuminate\Http\JsonResponse
     */
    public function queryStreet(int $city, DPDQueryStreetRequest $request): JsonResponse
    {
        $data     = $this->client->findCityStreet($city, $request->query('query'), $request->query('session_id'));
        $response = $this->responseOrFail($data, 'streetList');
        return response()->json($response);
    }

    /**
     * Query Terminal City.
     *
     * @param \SergeevPasha\DPD\Http\Requests\DPDQueryReceivePointCityRequest $request
     *
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return \Illuminate\Http\JsonResponse
     */
    public function queryReceivePointCity(DPDQueryReceivePointCityRequest $request): JsonResponse
    {
        $data     = $this->client->findReceivePointCity($request->query('query'));
        $response = $this->responseOrFail($data);
        return response()->json($response);
    }

    /**
     * Query Receive Points.
     *
     * @param \SergeevPasha\DPD\Http\Requests\DPDQueryReceivePointsRequest $request
     *
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReceivePoints(DPDQueryReceivePointsRequest $request): JsonResponse
    {
        $data     = $this->client->getReceivePoints($request->query('bounds'), $request->query('city'));
        $response = $this->responseOrFail($data);
        return response()->json($response);
    }

    /**
     * Get Terminals.
     *
     * @param \SergeevPasha\DPD\Http\Requests\DPDTerminalRequest $request
     *
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTerminals(DPDTerminalRequest $request): JsonResponse
    {
        $data     = $this->client->getTerminals($request->query('bounds'), $request->query('city'));
        $response = $this->responseOrFail($data);
        return response()->json($response);
    }

    /**
     * Calculate delivery.
     *
     * @param \SergeevPasha\DPD\Http\Requests\DPDCalculatePriceRequest $request
     *
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumKeyException
     * @throws \Illuminate\Validation\ValidationException
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateDeliveryPrice(DPDCalculatePriceRequest $request): JsonResponse
    {
        $delivery = Delivery::fromArray($request->all());
        try {
            $data     = $this->client->getPrice($delivery);
            $response = $this->responseOrFail($data, 'return');
            return response()->json($response);
        // @codeCoverageIgnoreStart
        } catch (Exception $exception) {
            throw ValidationException::withMessages(
                [$exception->detail->ServiceCostFault2->code => $exception->detail->ServiceCostFault2->message]
            );
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Find track by number.
     *
     * @param \SergeevPasha\DPD\Http\Requests\DPDFindByTrackNumberRequest $request
     *
     * @throws \Illuminate\Validation\ValidationException
     * @return \Illuminate\Http\JsonResponse
     */
    public function findByTrackNumber(DPDFindByTrackNumberRequest $request): JsonResponse
    {
        $data = $request->all();
        try {
            $data = $this->client->findByTrackNumber($data['number']);
            return response()->json($data);
        // @codeCoverageIgnoreStart
        } catch (Exception $exception) {
            throw ValidationException::withMessages(
                [$exception->detail->ServiceCostFault2->code => $exception->detail->ServiceCostFault2->message]
            );
            // @codeCoverageIgnoreEnd
        }
    }
}
