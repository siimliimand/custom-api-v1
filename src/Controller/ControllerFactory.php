<?php

namespace App\Controller;

use App\Configuration\RoutesConfigurationInterface;
use App\Exception\InvalidRouteException;
use App\RequestHandler\Request;

class ControllerFactory
{
    /**
     * @param array $routeData
     * @param Request $request
     * @return array
     * @throws InvalidRouteException
     */
    public static function callControllerByRouteData(array $routeData, Request $request): array
    {
        $controllerClass = $routeData[RoutesConfigurationInterface::CONTROLLER] ?? null;
        $action = $routeData[RoutesConfigurationInterface::ACTION] ?? null;
        $method = $routeData[RoutesConfigurationInterface::METHOD] ?? null;

        if ($controllerClass === null || $action === null || $method === null) {
            throw new InvalidRouteException(
                translate('messages.error.invalid_route')
            );
        }

        if ($request->getMethod() === Request::METHOD_OPTIONS) {
            return [];
        }

        if ($method !== $request->getMethod()) {
            throw new InvalidRouteException(
                translate('messages.error.invalid_method', [
                    'method' => $method
                ])
            );
        }

        $parameters = $routeData[RoutesConfigurationInterface::PARAMETERS] ?? null;
        if ($parameters && count($parameters) > 0) {
            foreach ($parameters as $key => $value) {
                $request->request->set($key, $value);
            }
        }

        $controller = new $controllerClass();
        return $controller->{$action}($request);
    }
}