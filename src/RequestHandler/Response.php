<?php

namespace App\RequestHandler;

use function function_exists;

class Response
{
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_ACCEPTED = 202;
    public const HTTP_MOVED_PERMANENTLY = 301;
    public const HTTP_TEMPORARY_REDIRECT = 307;
    public const HTTP_PERMANENT_REDIRECT = 308;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_TOO_MANY_REQUESTS = 429;

    public const SERVER_PROTOCOL = '2';

    public SetterGetter $headers;
    public string $content = '';
    public int $statusCode = self::HTTP_OK;

    public static array $statusTexts = [
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        301 => 'Moved Permanently',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        429 => 'Too Many Requests'
    ];

    public function __construct()
    {
        $this->headers = new SetterGetter(SetterGetter::TYPE_HEADERS);
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    public function send(): void
    {
        $this->sendHeaders();
        $this->sendContent();

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

    protected function sendHeaders(): void
    {
        $headers = $this->headers->getData();
        foreach ($headers as $key => $value) {
            header($key . ':' . $value);
        }

        // status
        header(
            sprintf(
                'HTTP/%s %s %s',
                static::SERVER_PROTOCOL,
                $this->statusCode,
                self::$statusTexts[$this->statusCode] ?? 'unknown status'
            ),
            true,
            $this->statusCode
        );
    }

    public function sendContent(): void
    {
        echo $this->content;
    }
}