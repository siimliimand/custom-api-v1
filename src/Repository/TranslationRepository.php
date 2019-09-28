<?php

namespace App\Repository;

use App\DB\DB;

class TranslationRepository
{
    public const TABLE_NAME = 'translations';

    /**
     * @param string $key
     * @param int $languageId
     * @return string|null
     */
    public static function get(string $key, int $languageId): ?string
    {
        $tableName = static::TABLE_NAME;
        $sql = "
        SELECT `value` 
          FROM `$tableName`
         WHERE `key` = :key
           AND `language_id` = :language_id
        ";
        $params = [
            'key' => $key,
            'language_id' => $languageId
        ];
        $stmt = DB::execute($sql, $params);
        $data = $stmt ? $stmt->fetch() : false;

        return $data && isset($data['value']) ? $data['value'] : null;
    }

    /**
     * @param string $key
     * @param int $languageId
     * @param string $value
     * @return bool
     */
    public static function set(string $key, int $languageId, string $value): bool
    {
        $tableName = static::TABLE_NAME;
        $sql = "
        INSERT INTO `$tableName` SET 
        `key` = :key,
        `language_id` = :language_id,
        `value` = :value,
        `created_at` = NOW(),
        `updated_at` = NOW()
        ";
        $params = [
            'key' => $key,
            'language_id' => $languageId,
            'value' => $value
        ];

        return DB::execute($sql, $params) !== null;
    }
}