<?php

namespace App\Cache;

use Closure;
use TinyRedisClient;

class CacheAdapter
{
    protected static $client;

    /**
     * @param string $key
     * @return string|null
     */
    public static function get(string $key): ?string
    {
        $cacheKey = md5($key);
        $client = static::getClient();

        return $client->get($cacheKey);
    }

    /**
     * @param string $key
     * @param string $value
     */
    public static function set(string $key, string $value): void
    {
        $cacheKey = md5($key);
        $client = static::getClient();
        $client->set($cacheKey, $value);
    }

    /**
     * @return TinyRedisClient
     */
    public static function getClient(): TinyRedisClient
    {
        if (static::$client === null) {
            static::$client = new TinyRedisClient('127.0.0.1:6379');
        }

        return static::$client;
    }

    /**
     * @param string $key
     * @return array|null
     */
    public static function getArray(string $key): ?array
    {
        $value = static::get($key);
        $array = json_decode($value, true);

        if(is_array($array)) {
            return $array;
        }

        return null;
    }

    /**
     * @param string $key
     * @param array $array
     */
    public static function setArray(string $key, array $array): void
    {
        $value = json_encode($array);
        static::set($key, $value);
    }

    /**
     * @param string $key
     * @param Closure $closure
     * @return string|null
     */
    public static function getOrCallClosure(string $key, Closure $closure): ?string
    {
        $value = static::get($key);
        if($value === null) {
            $value = $closure();
            static::set($key, $value);
        }

        return $value;
    }

    /**
     * @param string $key
     * @param Closure $closure
     * @return array|null
     */
    public static function getArrayOrCallClosure(string $key, Closure $closure): ?array
    {
        $array = static::getArray($key);
        if ($array === null) {
            $array = $closure();
            static::setArray($key, $array);
        }

        return $array;
    }
}