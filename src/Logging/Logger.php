<?php

namespace App\Logging;

use App\Repository\ActivityLogRepository;
use App\RequestHandler\Request;
use Exception;

class Logger
{

    /**
     * @param Exception $exception
     * @param Request $request
     * @return bool
     */
    public static function logError(Exception $exception, Request $request): bool
    {
        $requestUri = $request->server->get('REQUEST_URI');
        $data = [
            'REQUEST_URI' => $requestUri,
            'IP' => getClientIP()
        ];

        return true;
    }

    /**
     * @param string $controller
     * @param string $action
     * @param string $method
     * @param int|null $userId
     * @param int|null $languageId
     * @param array $params
     * @param array $response
     */
    public static function logActivity(
        string $controller,
        string $action,
        string $method,
        ?int $userId = null,
        ?int $languageId = null,
        array $params = [],
        array $response = []
    ): void {
        $logPath = ROOT_PATH . 'logs/';
        $logFile = $logPath . 'activity.log';

        $data = ActivityLogRepository::getInsertParams(
            $controller,
            $action,
            $method,
            $userId,
            $languageId,
            $params,
            $response
        );
        $json = json_encode($data, JSON_THROW_ON_ERROR, 512);

        $fp = fopen($logFile, 'a+b');

        if (flock($fp, LOCK_EX)) {
            fwrite($fp, $json.PHP_EOL);
            fflush($fp);
            flock($fp, LOCK_UN);
        }

        fclose($fp);
    }

}