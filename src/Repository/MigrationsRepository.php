<?php

namespace App\Repository;

use App\DB\DB;

class MigrationsRepository
{
    public const TABLE_NAME = 'migrations';

    /**
     * @param string $key
     * @return bool
     */
    public static function hasMigration(string $key): bool
    {
        $migrationsTableName = static::TABLE_NAME;
        $stmt = DB::execute("SELECT `id` FROM `$migrationsTableName` WHERE `migration` = :migration", [
            'migration' => $key
        ]);
        return $stmt !== null && $stmt->fetch() !== false;
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function insertMigration(string $key): bool
    {
        $migrationsTableName = static::TABLE_NAME;
        return DB::execute("INSERT INTO `$migrationsTableName` (`migration`) VALUES (:migration)", [
            'migration' => $key
        ]) !== null;
    }

    /**
     * @param string $key
     * @param string $sql
     * @return bool
     */
    public static function migrate(string $key, string $sql): bool
    {
        if(DB::execute($sql) !== null) {
            return static::insertMigration($key);
        }

        return false;
    }
}