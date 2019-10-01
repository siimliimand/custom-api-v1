<?php

use App\Cache\CacheAdapter;
use App\Configuration\Configuration;
use App\Repository\LanguageRepository;
use App\Repository\TranslationRepository;

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

function translate(string $key, string $languageCode = 'en', array $params = []): string
{
    $defaultLanguageId = 1;
    $translationKey = 'translation.' . $key;
    $cacheKey = $translationKey . '-' . $languageCode;
    $translation = CacheAdapter::get($cacheKey);
    if ($translation !== null) {
        return vsprintf($translation, $params);
    }

    $languageId = LanguageRepository::getLanguageId($languageCode);
    if ($languageId === null) {
        $languageId = $defaultLanguageId;
    }

    $translation = TranslationRepository::get($key, $languageId);
    if ($translation !== null) {
        CacheAdapter::set($cacheKey, $translation, 0);
        return vsprintf($translation, $params);
    }

    $default = Configuration::get($translationKey);
    if ($default !== null) {
        if ($languageId === $defaultLanguageId) {
            TranslationRepository::set($key, $languageId, $default);
            CacheAdapter::set($cacheKey, $default, 0);
        }
        $default = vsprintf($default, $params);
    }
    return $default . ' | TODO: Translate it';
}

function appGet(string $key): string
{
    $appKey = 'app.' . $key;
    return Configuration::get($appKey);
}

function getMyMicroTime(): int
{
    [$msec, $sec] = explode(' ', microtime());

    return (int) $sec . str_replace('0.', '', $msec);
}

function getClientIP()
{
    $ipaddress = 'UNKNOWN';
    $keys=array('HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR','HTTP_X_FORWARDED','HTTP_FORWARDED_FOR','HTTP_FORWARDED','REMOTE_ADDR');
    foreach($keys as $k)
    {
        if (isset($_SERVER[$k]) && !empty($_SERVER[$k]) && filter_var($_SERVER[$k], FILTER_VALIDATE_IP))
        {
            $ipaddress = $_SERVER[$k];
            break;
        }
    }
    return $ipaddress;
}