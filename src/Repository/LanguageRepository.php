<?php

namespace App\Repository;

use App\Cache\CacheAdapter;
use App\DB\DB;

class LanguageRepository
{
    public const TABLE_NAME = 'languages';

    /**
     * @param string $languageCode
     * @return array
     */
    public static function get(string $languageCode): array
    {
        $cacheKey = 'languages-' . $languageCode;
        return CacheAdapter::getArrayOrCallClosure($cacheKey, static function () use ($languageCode) {
            $tableName = static::TABLE_NAME;
            $sql = "
            SELECT `id`, `code`, `name`
              FROM `$tableName`
             WHERE `status_id` = :status_id
               AND `code` = :code
            ";
            $params = [
                'status_id' => StatusRepository::getStatusIdByCode(StatusRepository::STATUS_ACTIVE),
                'code' => $languageCode
            ];
            $stmt = DB::execute($sql, $params);
            $data = $stmt !== null ? $stmt->fetch() : false;

            return $data ?? [];
        }, 0);
    }

    /**
     * @param string $languageCode
     * @return int|null
     */
    public static function getLanguageId(string $languageCode): ?int
    {
        $data = static::get($languageCode);

        return $data['id'] ?? null;
    }
}