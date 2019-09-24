<?php

namespace App\RequestHandler;

use App\Controller\ControllerFactory;
use App\Exception\InvalidRouteException;
use App\Exception\UnauthorizedException;
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
            $response = ControllerFactory::callControllerByRouteData($routeData, $request);
            if ($request->getMethod() === Request::METHOD_OPTIONS) {
                $this->sendOptionsResponse($response, $request);
            } else {
                $this->sendOkResponse($response, $request);
            }
        } catch (InvalidRouteException $e) {
            $this->sendErrorResponse($e->getMessage(), $request);
        } catch (UnauthorizedException $e) {
            $this->sendUnauthorizedResponse($e->getMessage(), $request);
        } catch (Exception $e) {
            $this->sendErrorResponse($e->getMessage(), $request);
        }
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

        if (isset($requestHeaders['Access-Control-Request-Headers'])) {
            $response->headers->set('Access-Control-Allow-Headers', $requestHeaders['Access-Control-Request-Headers']);
        } else {
            $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type');
        }
        if (isset($requestHeaders['Access-Control-Request-Method'])) {
            $response->headers->set('Access-Control-Allow-Methods', $requestHeaders['Access-Control-Request-Method']);
        }

        $response->setStatusCode(Response::HTTP_OK);
        $response->setContent(empty($content) ? '' : json_encode($content));
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
        $response->headers->set('Access-Control-Allow-Origin', $request->headers->get('Origin'));
        $response->setContent(json_encode($content));
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