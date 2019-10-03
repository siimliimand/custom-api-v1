<?php

namespace App\Repository;

use App\DB\DB;

class ActivityLogRepository
{
    public const TABLE_NAME = 'activity_log';

    public const TYPE_INFO = 'info';
    public const TYPE_WARNING = 'warning';
    public const TYPE_ERROR = 'error';

    /**
     * @param string $controller
     * @param string $action
     * @param string $method
     * @param int|null $userId
     * @param array $params
     * @param array|null $response
     */
    public static function insert(
        string $controller,
        string $action,
        string $method,
        ?int $userId = null,
        array $params = [],
        array $response = []
    ): void {
        $sql = static::getInsertSql();
        $parameters = static::getInsertParams($controller, $action, $method, $userId, $params, $response);
        DB::execute($sql, $parameters);
    }

    /**
     * @param array $paramsArray
     */
    public static function insertMultiple(array $paramsArray): void
    {
        $sql = static::getInsertSql();
        DB::executeMultiple($sql, $paramsArray);
    }

    /**
     * @return string
     */
    public static function getInsertSql(): string
    {
        $tableName = static::TABLE_NAME;
        return "
        INSERT INTO `$tableName` 
        (`controller`, `action`, `method`, `language_id`, `user_id`, `params`, `response`, `created_at`)
        VALUES
        (:controller, :action, :method, :language_id, :user_id, :params, :response, NOW())
        ";
    }

    /**
     * @param string $controller
     * @param string $action
     * @param string $method
     * @param int|null $userId
     * @param int|null $languageId
     * @param array $params
     * @param array $response
     * @return array
     */
    public static function getInsertParams(
        string $controller,
        string $action,
        string $method,
        ?int $userId = null,
        ?int $languageId = null,
        array $params = [],
        array $response = []
    ): array
    {
        return [
            'controller' => $controller,
            'action' => $action,
            'method' => $method,
            'user_id' => $userId,
            'language_id' => $languageId,
            'params' => $params,
            'response' => json_encode($response, JSON_THROW_ON_ERROR, 512)
        ];
    }
}