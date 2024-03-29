<?php

namespace App\RequestHandler;

use App\Configuration\RoutesConfigurationInterface;
use App\Controller\ControllerFactory;
use App\Exception\InvalidRouteException;
use App\Exception\UnauthorizedException;
use App\Logging\Logger;
use App\Repository\LanguageRepository;
use App\Repository\UserRepository;
use App\Route\RouteManager;
use Exception;

class RequestHandler
{
    public const CONTENT_TYPE_JSON = 'application/json';

    /**
     * @param Request $request
     */
    public function handle(Request $request): void
    {
        $routeData = RouteManager::getRouteData($request);
        try {
            static::checkApiKey($request, $routeData);

            $response = ControllerFactory::callControllerByRouteData($routeData, $request);
            if ($request->getMethod() === Request::METHOD_OPTIONS) {
                $this->sendOptionsResponse($response, $request);
            } else {
                $this->logActivity($request, $routeData, $response);
                $this->sendOkResponse($response, $request);
            }
        } catch (InvalidRouteException $e) {
            Logger::logError($e, $request);
            $this->sendErrorResponse($e->getMessage(), $request);
        } catch (UnauthorizedException $e) {
            Logger::logError($e, $request);
            $this->sendUnauthorizedResponse($e->getMessage(), $request);
        } catch (Exception $e) {
            Logger::logError($e, $request);
            $this->sendErrorResponse($e->getMessage(), $request);
        }
    }

    /**
     * @param Request $request
     * @param array $routeData
     * @throws UnauthorizedException
     */
    protected static function checkApiKey(Request $request, array $routeData): void
    {
        $apiToken = $request->headers->get('X-API-KEY', null);
        if ($routeData[RoutesConfigurationInterface::ACCESS] === RoutesConfigurationInterface::ACCESS_PRIVATE) {
            $user = $apiToken !== null ? UserRepository::getUserIdByApiToken($apiToken) : null;
            if ($user === null) {
                throw new UnauthorizedException(
                    translate('messages.error.unauthorized')
                );
            }
        }
    }

    /**
     * @param Request $request
     * @param array $routeData
     * @param array $response
     */
    protected function logActivity(Request $request, array $routeData, array $response): void
    {
        if (env('LOG_ACTIVITY', false) === false) {
            return ;
        }

        $parameters = $routeData[RoutesConfigurationInterface::PARAMETERS] ?? [];

        $apiToken = $parameters[RoutesConfigurationInterface::PARAMETER_TOKEN] ?? null;
        $userId = $apiToken !== null ? UserRepository::getUserIdByApiToken($apiToken) : null;

        $languageCode = $parameters[RoutesConfigurationInterface::PARAMETER_LANGUAGE_CODE] ?? 'en';
        $languageId = $languageCode !== null ? LanguageRepository::getLanguageId($languageCode) : null;

        Logger::logActivity(
            $routeData[RoutesConfigurationInterface::CONTROLLER] ?? '',
            $routeData[RoutesConfigurationInterface::ACTION] ?? '',
            $routeData[RoutesConfigurationInterface::METHOD] ?? '',
            $userId,
            $languageId,
            $request->request->getData(),
            $response
        );
    }

    /**
     * @param array $content
     * @param Request $request
     */
    protected function sendOptionsResponse(array $content, Request $request): void
    {
        $requestHeaders = $request->headers->all();
        $response = new Response();
        $response->headers->set('Access-Control-Allow-Origin', $requestHeaders['Origin']);
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Max-Age', '86400');

        if (isset($requestHeaders['access-control-request-headers'])) {
            $response->headers->set('Access-Control-Allow-Headers', $requestHeaders['access-control-request-headers']);
        } else {
            $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type');
        }
        if (isset($requestHeaders['access-control-request-method'])) {
            $response->headers->set('Access-Control-Allow-Methods', $requestHeaders['access-control-request-method']);
        }

        $response->setStatusCode(Response::HTTP_OK);
        $response->setContent(empty($content) ? '' : json_encode($content, JSON_THROW_ON_ERROR, 512));
        $response->send();
    }

    /**
     * @param array $content
     * @param int $statusCode
     * @param Request $request
     */
    protected function sendResponse(array $content, int $statusCode, Request $request): void
    {
        $response = new Response();
        $response->setStatusCode($statusCode);
        $response->headers->set('Content-Type', static::CONTENT_TYPE_JSON);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'deny');
        $response->headers->set('Access-Control-Allow-Origin', $request->headers->get('Origin'));
        $response->headers->set('x-api-key', $request->headers->get('x-api-key'));
        $response->setContent(json_encode($content, JSON_THROW_ON_ERROR, 512));
        $response->send();
    }

    /**
     * @param array $content
     * @param Request $request
     */
    protected function sendOkResponse(array $content, Request $request): void
    {
        $this->sendResponse($content, Response::HTTP_OK, $request);
    }

    /**
     * @param string $message
     * @param Request $request
     */
    protected function sendErrorResponse(string $message, Request $request): void
    {
        $content = [
            'error' => $message
        ];
        $this->sendResponse($content, Response::HTTP_NOT_FOUND, $request);
    }

    /**
     * @param string $message
     * @param Request $request
     */
    protected function sendUnauthorizedResponse(string $message, Request $request): void
    {
        $content = [
            'error' => $message
        ];
        $this->sendResponse($content, Response::HTTP_UNAUTHORIZED, $request);
    }
}