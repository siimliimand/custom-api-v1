<?php

namespace App\Controller;

use App\RequestHandler\Request;

abstract class AbstractController implements ControllerInterface
{

    /**
     * @param Request $request
     * @return bool
     */
    public function isPostRequest(Request $request): bool
    {
        return $request->getMethod() === static::METHOD_POST;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function isGetRequest(Request $request): bool
    {
        return $request->getMethod() === static::METHOD_GET;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function isPutRequest(Request $request): bool
    {
        return $request->getMethod() === static::METHOD_PUT;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function isDeleteRequest(Request $request): bool
    {
        return $request->getMethod() === static::METHOD_DELETE;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function isOptionsRequest(Request $request): bool
    {
        return $request->getMethod() === static::METHOD_OPTIONS;
    }

}