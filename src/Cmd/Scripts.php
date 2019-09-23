<?php

namespace App\Cmd;

class Scripts
{
    public static function preInstall($event): void
    {
        $rootPath = __DIR__ . '/../../';
        $dotEnv = $rootPath . '.env';
        $exampleEnv = $rootPath . 'example.env';
        if (file_exists($dotEnv)) {
            echo 'file exists';
        } else if (!file_exists($exampleEnv) ||
            !copy($exampleEnv, $dotEnv)) {
            echo "failed to copy example.env\n";
        }
    }
}