<?php

namespace App\Controller;

use App\Cache\CacheAdapter;
use App\DB\DB;

class DefaultController
{
    /**
     * @return array
     */
    public function index(): array
    {
        $cacheKey = 'get-all-users';
        $users = CacheAdapter::getArrayOrCallClosure($cacheKey, static function () {
            $stmt = DB::execute('SELECT name FROM users');
            $users = $stmt->fetchAll();

            return $users;
        });

        return [
            'action' => 'Default:index',
            'users' => $users
        ];
    }
}