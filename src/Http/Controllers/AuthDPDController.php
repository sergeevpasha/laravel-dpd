<?php

declare(strict_types=1);

namespace SergeevPasha\DPD\Http\Controllers;

use Illuminate\Http\JsonResponse;
use SergeevPasha\DPD\Libraries\DPDClient;
use Illuminate\Validation\ValidationException;

class AuthDPDController
{
    /**
     * DPD Client Instance.
     *
     * @var \SergeevPasha\DPD\Libraries\DPDClient
     */
    private $client;

    public function __construct(DPDClient $client)
    {
        $this->client = $client;
    }

    /**
     * Authorize DPD User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        $session = $this->client->authorize();
        if (!$session) {
            throw ValidationException::withMessages([
                'login' => trans('dpd::messages.invalid', ['attribute' => 'login']),
                'password'   => trans('dpd::messages.invalid', ['attribute' => 'password'])
            ]);
        }
        $response = [
            'message' => trans('dpd::messages.success_login'),
            'session' => $session,
        ];
        return response()->json($response);
    }
}
