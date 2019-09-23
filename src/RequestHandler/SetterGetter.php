<?php

namespace App\RequestHandler;

class SetterGetter
{
    public const TYPE_GET = '_GET';
    public const TYPE_POST = '_POST';
    public const TYPE_SERVER = '_SERVER';
    public const TYPE_COOKIE = '_COOKIE';
    public const TYPE_FILES = '_FILES';
    public const TYPE_HEADERS = 'getallheaders';

    protected $data = [];
    protected $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @param string $key
     * @param $value
     */
    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        $value = $this->data[$key] ?? null;
        if ($value === null) {
            if ($this->type === static::TYPE_HEADERS) {
                $fn = $this->type;
                return $fn()[$key] ?? null;
            }
            return $GLOBALS[$this->type][$key] ?? $default;
        }

        if ($value === null) {
            return $default;
        }

        return $value;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        if ($this->type === static::TYPE_HEADERS) {
            $fn = $this->type;
            $data = $fn();
        } else {
            $data = $GLOBALS[$this->type];
        }

        return array_replace($data, $this->data);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}