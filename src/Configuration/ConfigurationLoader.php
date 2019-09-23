<?php

namespace App\Configuration;

use Exception;

class ConfigurationLoader
{
    protected const CONFIGURATION_EXTENSION = '.php';

    /**
     * Load configurations
     */
    public function load(): void
    {
        $files = array_diff(scandir(CONFIG_PATH), array('.', '..'));
        foreach ($files as $file) {
            $this->loadFile($file);
        }
    }

    /**
     * @param string $fileName
     */
    protected function loadFile(string $fileName): void
    {
        try {
            $path = CONFIG_PATH . $fileName;
            $key = basename($path, static::CONFIGURATION_EXTENSION);
            if ($key . static::CONFIGURATION_EXTENSION === $fileName) {
                $data = require $path;
                Configuration::add($key, $data);
            }
        } catch (Exception $exception) {
            // Error
        }
    }
}