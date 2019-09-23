<?php

namespace App\Controller;

use App\Cache\CacheAdapter;
use App\Configuration\Configuration;
use App\Logging\Logger;
use PDO;
use PDOException;

class DefaultController
{
    /**
     * @return array
     */
    public function index(): array
    {
        $cacheKey = 'get-all-users';
        $users = CacheAdapter::getArrayOrCallClosure($cacheKey, static function () {
            $db = Configuration::get('app.db');
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

            try {
                $pdo = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                Logger::logError($e);
                return [];
            }

            $users = [];
            $stmt = $pdo->query('SELECT name FROM users');
            while ($row = $stmt->fetch()) {
                $users[] = $row['name'];
            }

            return $users;
        });

        return [
            'action' => 'Default',
            'users' => $users
        ];
    }
}