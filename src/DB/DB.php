<?php

namespace App\DB;

use App\Configuration\Configuration;
use Exception;
use PDO;
use PDOException;
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
            try {
                $stmt = static::getPdo()->prepare($sql);
                if ($stmt && $stmt->execute($params)) {
                    return $stmt;
                }
            } catch(PDOException $exception) {
                echo $exception->getMessage() . "\n";
            }
        }

        return null;
    }

    /**
     * @param string $sql
     * @param array $paramsArray
     * @return bool
     */
    public static function executeMultiple(string $sql, array $paramsArray): bool
    {
        if (static::getPdo() !== null) {
            $pdo = static::getPdo();
            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare($sql);
                foreach ($paramsArray as $params) {
                    if (is_array($params) === false) {
                        continue;
                    }
                    $stmt->execute($params);
                }
                $pdo->commit();
                return true;
            } catch(Exception $e) {
                $pdo->rollBack();
            }
        }

        return false;
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