<?php

namespace App\Cache;

use App\Configuration\Configuration;
use Closure;
use TinyRedisClient;

class CacheAdapter
{
    public const TAG_SEPARATOR = ',';
    protected static ?TinyRedisClient $client = null;

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
     * @param int|null $ex
     */
    public static function set(string $key, string $value, int $ex = null): void
    {
        if ($ex === null) {
            $ex = env('REDIS_EX_SECONDS', 3600);
        }
        $cacheKey = md5($key);
        $client = static::getClient();
        if ($ex === 0) {
            $client->set($cacheKey, $value);
        } else {
            $client->setex($cacheKey, $ex, $value);
        }
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
     * @param int|null $ex
     */
    public static function setArray(string $key, array $array, int $ex = null): void
    {
        $value = json_encode($array);
        static::set($key, $value, $ex);
    }

    /**
     * @param string $key
     * @param Closure $closure
     * @param int|null $ex
     * @return array|null
     */
    public static function getArrayOrCallClosure(string $key, Closure $closure, int $ex = null): ?array
    {
        $array = static::getArray($key);
        if ($array === null) {
            $array = $closure();
            if (empty($array) === false) {
                static::setArray($key, $array, $ex);
            }
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

    /**
     * @param string $key
     * @param string $itemKey
     */
    public static function addTag(string $key, string $itemKey): void
    {
        $cacheKey = md5($key);
        $itemCacheKey = md5($itemKey);
        $client = static::getClient();
        $client->append($cacheKey, $itemCacheKey . static::TAG_SEPARATOR);
    }
}