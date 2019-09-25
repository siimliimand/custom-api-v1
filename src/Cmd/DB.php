<?php

namespace App\Cmd;

use App\Repository\MigrationsRepository;

class DB
{
    public const PATH = ROOT_PATH . 'db/migrations/';
    public const EXTENSION = '.sql';

    public static function migrate(): void
    {
        $files = array_diff(scandir(static::PATH), array('.', '..'));
        foreach ($files as $fileName) {
            $file = static::PATH . $fileName;
            $key = basename($file, static::EXTENSION);
            if (MigrationsRepository::hasMigration($key) === true) {
                continue;
            }
            $sql = file_get_contents($file);
            if(MigrationsRepository::migrate($key, $sql) === false) {
                die('Error: ' . $key . "\n");
            }

            echo 'OK: ' . $key . "\n";
        }

        echo "Done!\n";
    }
}