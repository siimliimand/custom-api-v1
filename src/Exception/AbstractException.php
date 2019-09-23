<?php

namespace App\Exception;

use Exception;
use Throwable;

abstract class AbstractException extends Exception
{
    public const MESSAGE = 'Invalid message';

    /**
     * AbstractException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        if ($message === '') {
            $message = static::MESSAGE;
        }

        parent::__construct($message, $code, $previous);
    }
}