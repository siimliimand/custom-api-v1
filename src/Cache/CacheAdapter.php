<?php

namespace App\Cache;

use App\Configuration\Configuration;
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
            $host = Configuration::get('app.redis.host');
            $port = Configuration::get('app.redis.port');
            static::$client = new TinyRedisClient($host . ':' . $port);
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

    /**
     * Clear all cache
     */
    public static function deleteAllKeys(): void
    {
        $client = static::getClient();
        $client->flushAll();
    }

    /**
     * @param string $key
     */
    public static function deleteKey(string $key): void
    {
        $cacheKey = md5($key);
        $client = static::getClient();
        $client->del($cacheKey);
    }
}