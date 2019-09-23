<?php

namespace App\Logging;

use Exception;
use Monolog\Handler\StreamHandler;

class Logger
{

    public const TYPE_ERROR = \Monolog\Logger::ERROR;
    public const TYPE_WARNING = \Monolog\Logger::WARNING;

    protected static $logger;

    /**
     * @param Exception $exception
     * @return bool
     */
    public static function logError(Exception $exception): bool
    {
        return static::logException($exception, static::TYPE_ERROR);
    }

    /**
     * @param Exception $exception
     * @return bool
     */
    public static function logWarning(Exception $exception): bool
    {
        return static::logException($exception, static::TYPE_WARNING);
    }

    /**
     * @param Exception $exception
     * @param int $type
     * @return bool
     */
    public static function logException(Exception $exception, int $type = self::TYPE_ERROR): bool
    {
        $logger = static::getLogger();
        if ($logger) {
            switch ($type) {
                case static::TYPE_ERROR:
                    $logger->error($exception->getMessage(), $exception->getTrace());
                    break;
                case static::TYPE_WARNING:
                    $logger->warning($exception->getMessage(), $exception->getTrace());
                    break;
                default:
                    return false;
            }

            return true;
        }

        return false;
    }

    /**
     * TODO: Replace Monolog with Redis cache and db
     * @return \Monolog\Logger|null
     */
    public static function getLogger(): ?\Monolog\Logger
    {
        if (static::$logger !== null) {
            return static::$logger;
        }

        $log = new \Monolog\Logger('app');

        try {
            $log->pushHandler(new StreamHandler(ROOT_PATH . 'logs/test.log', \Monolog\Logger::WARNING));
        } catch (Exception $e) {
            return null;
        }

        static::$logger = $log;

        return $log;
    }
}