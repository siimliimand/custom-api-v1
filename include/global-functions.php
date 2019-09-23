<?php

use App\Configuration\Configuration;

function dump(...$data): void
{
    if (Configuration::get(Configuration::APP_DEBUG) === true) {
        __dump(...$data);
    }
}

function dd(...$data): void
{
    if (Configuration::get(Configuration::APP_DEBUG) === true) {
        __dump(...$data);
        exit;
    }
}

function __dump(...$data): void
{
    echo '<pre style="border: 1px solid #a0a0a0;padding: 10px;background: #eee; word-wrap: break-word; white-space: pre-wrap;">';
    $backtrace = debug_backtrace();
    $file = $backtrace[1]['file'];
    $line = $backtrace[1]['line'];
    echo "{$file} (Line: {$line})\n";
    print_r($data);
    echo '</pre>';
}

function translate(string $key, array $params = []): string
{
    $default = Configuration::get($key);
    if ($default !== null) {
        $default = vsprintf($default, $params);
        //TODO: Call from db
    }
    return $default . ' | TODO: Translate it';
}