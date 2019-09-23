<?php

namespace App\Controller;

use App\RequestHandler\Request;

interface ControllerInterface
{
    public const METHOD_POST = Request::METHOD_POST;
    public const METHOD_GET = Request::METHOD_GET;
    public const METHOD_PUT = Request::METHOD_PUT;
    public const METHOD_DELETE = Request::METHOD_DELETE;
    public const METHOD_OPTIONS = Request::METHOD_OPTIONS;

    /**
     * @param Request $request
     * @return bool
     */
    public function isPostRequest(Request $request): bool;

    /**
     * @param Request $request
     * @return bool
     */
    public function isGetRequest(Request $request): bool;

    /**
     * @param Request $request
     * @return bool
     */
    public function isPutRequest(Request $request): bool;

    /**
     * @param Request $request
     * @return bool
     */
    public function isDeleteRequest(Request $request): bool;

    /**
     * @param Request $request
     * @return bool
     */
    public function isOptionsRequest(Request $request): bool;
}