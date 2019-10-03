<?php

namespace App\Route;

use App\Configuration\Configuration;
use App\Configuration\RoutesConfigurationInterface;
use Closure;
use App\RequestHandler\Request;

class RouteManager
{
    /** @var array $routes */
    protected static array $routes = [];

    /**
     * @param Request $request
     * @return array
     */
    public static function getRouteData(Request $request): array
    {
        $requestUri = $request->server->get('REQUEST_URI');
        $parts = explode('/', $requestUri);
        $routes = static::getRoutes();
        return static::findRouteData($parts, $routes);
    }

    /**
     * @param array $routeParts
     * @param array $routes
     * @param Closure|null $closure
     * @return array
     */
    protected static function findRouteData(array $routeParts, array $routes, Closure $closure = null): array
    {
        $routePart = array_shift($routeParts);
        if ($routePart === '') {
            $routePart = '/';
        }
        $countRouteParts = count($routeParts);
        if ($countRouteParts === 1 && empty($routeParts[0])) {
            $routeParts = [];
            $countRouteParts = 0;
        }
        $responseData = [];

        foreach ($routes as $key => $value) {
            if (is_array($value) === false) {
                break;
            }

            if ($key === $routePart) {
                $responseData = static::findRouteDataKeyEqRoutePart($countRouteParts, $value, $routeParts);
                break;
            }

            if ($key === RoutesConfigurationInterface::REGEX) {
                $responseData = static::findRouteDataKeyEqRegex($countRouteParts, $value, $routeParts, $routePart);
                break;
            }
        }

        if ($closure !== null) {
            $responseData = $closure($responseData);
        }

        return $responseData;
    }

    /**
     * @param int $countRouteParts
     * @param array $value
     * @param array $routeParts
     * @return array
     */
    protected static function findRouteDataKeyEqRoutePart(
        int $countRouteParts,
        array $value,
        array $routeParts
    ): array {
        $responseData = [];

        if ($countRouteParts > 0 && array_key_exists(RoutesConfigurationInterface::ROUTES, $value)) {
            $responseData = static::findRouteData($routeParts, $value[RoutesConfigurationInterface::ROUTES]);
        } else if (array_key_exists(RoutesConfigurationInterface::DATA, $value)) {
            $responseData = $value[RoutesConfigurationInterface::DATA];
        }

        return $responseData;
    }

    /**
     * @param int $countRouteParts
     * @param array $value
     * @param array $routeParts
     * @param string $routePart
     * @return array
     */
    protected static function findRouteDataKeyEqRegex(
        int $countRouteParts,
        array $value,
        array $routeParts,
        string $routePart
    ): array {
        $responseData = [];
        $regex = $value[RoutesConfigurationInterface::REGEX_DATA][RoutesConfigurationInterface::VALUE];
        $name = $value[RoutesConfigurationInterface::REGEX_DATA][RoutesConfigurationInterface::NAME];

        if (preg_match($regex, $routePart)) {
            if ($countRouteParts > 0 && array_key_exists(RoutesConfigurationInterface::ROUTES, $value)) {
                $responseData = static::findRouteData(
                    $routeParts,
                    $value[RoutesConfigurationInterface::ROUTES],
                    static function(array $responseData) use ($name, $routePart) {
                        $responseData[RoutesConfigurationInterface::PARAMETERS][$name] = $routePart;
                        return $responseData;
                    });
            } else if (array_key_exists(RoutesConfigurationInterface::DATA, $value)) {
                $responseData = $value[RoutesConfigurationInterface::DATA];
                $responseData[RoutesConfigurationInterface::PARAMETERS][$name] = $routePart;
            }
        }

        return $responseData;
    }

    /**
     * @return array
     */
    protected static function getRoutes(): array
    {
        if (empty(static::$routes)) {
            static::$routes = Configuration::get('routes');
        }

        return static::$routes;
    }
}