<?php

namespace App\Controller;

use App\Configuration\RoutesConfigurationInterface;
use App\Exception\UnauthorizedException;
use App\Logging\Logger;
use App\RequestHandler\Request;
use Exception;

class TestController
{
    public const ROUTE_NAME = 'test';

    public function test(): array
    {
        return [];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function index(Request $request): array
    {
        try {
            $data = [
                'total' => 102,
                'count' => 2,
                'page' => 11,
                'firstPage' => 1,
                'previousPage' => 10,
                'nextPage' => null,
                'lastPage' => 11,
                'perPage' => 10,
                'items' => [
                    [
                        'id' => 1
                    ],
                    [
                        'id' => 2
                    ]
                ]
            ];
        } catch(Exception $exception) {
            $data = [];
            Logger::logError($exception, $request);
        }

        return $data;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function show(Request $request): array
    {
        return [
            'id' => $request->request->get(RoutesConfigurationInterface::PARAMETER_ID)
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function edit(Request $request): array
    {
        return [
            'id' => $request->request->get(RoutesConfigurationInterface::PARAMETER_ID)
        ];
    }

    /**
     * @param Request $request
     * @return array
     * @throws UnauthorizedException
     */
    public function secure(Request $request): array
    {
        $token = $request->request->get(RoutesConfigurationInterface::PARAMETER_TOKEN, null);
        if ($token === null) {
            throw new UnauthorizedException(
                translate('messages.error.invalid_route')
            );
        }

        return [
            'id' => 1,
            'name' => 'Test Name',
            'lang' => $request->request->get(RoutesConfigurationInterface::PARAMETER_LANGUAGE_CODE)
        ];
    }
}