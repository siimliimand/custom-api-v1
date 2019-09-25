<?php

namespace App\Repository;

use App\Cache\CacheAdapter;
use App\DB\DB;

class StatusRepository
{
    public const TABLE_NAME = 'status';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_DELETED = 'deleted';

    /**
     * @param string $code
     * @return int|null
     */
    public static function getStatusIdByCode(string $code): ?int
    {
        $key = 'get-status-id-by-code-' . $code;
        $data = CacheAdapter::getArrayOrCallClosure($key, static function() use ($code) {
            $tableName = static::TABLE_NAME;
            $stmt = DB::execute("SELECT `id` FROM `$tableName` WHERE `code` = :code", [
                'code' => $code
            ]);
            if ($stmt) {
                return $stmt->fetch() ?? [];
            }

            return [];
        });
        return $data['id'] ?? null;
    }
}