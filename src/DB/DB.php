<?php

namespace App\DB;

use App\Configuration\Configuration;
use PDO;
use PDOStatement;

class DB
{
    /** @var PDO $pdo */
    protected static $pdo;

    /**
     * @param $sql
     * @param array $params
     * @return PDOStatement|null
     */
    public static function execute($sql, array $params = []): ?PDOStatement
    {
        if (static::getPdo() !== null) {
            $stmt = static::getPdo()->prepare($sql);
            if ($stmt && $stmt->execute($params)) {
                return $stmt;
            }
        }

        return null;
    }

    /**
     * @return PDO|null
     */
    public static function getPdo(): ?PDO
    {
        if (static::$pdo === null) {
            $db = Configuration::get('app.db');
            if ($db === null) {
                return null;
            }
            $host = $db['host'];
            $dbName = $db['db'];
            $user = $db['user'];
            $pass = $db['pass'];
            $charset = $db['charset'];

            $dsn = "mysql:host=$host;dbname=$dbName;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            static::$pdo = new PDO($dsn, $user, $pass, $options);
        }

        return static::$pdo;
    }
}