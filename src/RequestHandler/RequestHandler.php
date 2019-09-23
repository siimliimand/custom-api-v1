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
            $this->sendOkResponse($response);
        } catch (InvalidRouteException $e) {
            $this->sendErrorResponse($e->getMessage());
        } catch (UnauthorizedException $e) {
            $this->sendUnauthorizedResponse($e->getMessage());
        } catch (Exception $e) {
            dd([
                'message' => $e->getMessage(),
                'trace' => $e->getTrace()
            ]);
            $this->sendErrorResponse($e->getMessage());
        }
    }

    /**
     * @param array $content
     * @param int $statusCode
     */
    protected function sendResponse(array $content, int $statusCode): void
    {
        $response = new Response();
        $response->setStatusCode($statusCode);
        $response->headers->set('Content-Type', static::CONTENT_TYPE_JSON);
        $response->setContent(json_encode($content));
        $response->send();
    }

    /**
     * @param array $content
     */
    protected function sendOkResponse(array $content): void
    {
        $this->sendResponse($content, Response::HTTP_OK);
    }

    /**
     * @param string $message
     */
    protected function sendErrorResponse(string $message): void
    {
        $content = [
            'error' => $message
        ];
        $this->sendResponse($content, Response::HTTP_NOT_FOUND);
    }

    /**
     * @param string $message
     */
    protected function sendUnauthorizedResponse(string $message): void
    {
        $content = [
            'error' => $message
        ];
        $this->sendResponse($content, Response::HTTP_UNAUTHORIZED);
    }
}