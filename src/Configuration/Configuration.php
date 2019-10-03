<?php

namespace App\Configuration;

class Configuration
{
    public const APP_ENV = 'app.env';
    public const APP_DEBUG = 'app.debug';

    /** @var array $data */
    protected static array $data = [];

    /**
     * @param string $key
     * @return mixed|null
     */
    public static function get(string $key)
    {
        $keys = explode('.', $key);

        return static::getData($keys, static::$data);
    }

    /**
     * @param string $key
     * @param array $data
     */
    public static function add(string $key, array $data): void
    {
        static::$data[$key] = $data;
    }

    /**
     * @param array $keys
     * @param array $data
     * @return mixed|null
     */
    protected static function getData(array $keys, array $data)
    {
        $key = array_shift($keys);

        $exists = array_key_exists($key, $data);
        if ($exists && count($keys) > 0) {
            return static::getData($keys, $data[$key]);
        }

        return $exists ? $data[$key] : null;
    }
}