<?php

namespace App\Configuration;

interface AppConfigurationInterface
{
    public const PREFIX = 'app.';
    public const ENV = self::PREFIX . 'env';
    public const DEBUG = self::PREFIX . 'debug';
}