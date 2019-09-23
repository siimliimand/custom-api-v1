<?php

namespace App\RequestHandler;

class Request
{
    public const METHOD_POST = 'POST';
    public const METHOD_GET = 'GET';
    public const METHOD_PUT = 'PUT';
    public const METHOD_DELETE = 'DELETE';
    public const METHOD_OPTIONS = 'OPTIONS';

    public $request;
    public $query;
    public $server;
    public $cookie;
    public $files;
    public $headers;

    public function __construct()
    {
        $this->request = new SetterGetter(SetterGetter::TYPE_POST);
        $this->query = new SetterGetter(SetterGetter::TYPE_GET);
        $this->server = new SetterGetter(SetterGetter::TYPE_SERVER);
        $this->cookie = new SetterGetter(SetterGetter::TYPE_COOKIE);
        $this->files = new SetterGetter(SetterGetter::TYPE_FILES);
        $this->headers = new SetterGetter(SetterGetter::TYPE_HEADERS);
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->server->get('REQUEST_METHOD');
    }
}