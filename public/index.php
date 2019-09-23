<?php

require dirname(__DIR__).'/vendor/autoload.php';

use App\Configuration\Configuration;
use App\Configuration\ConfigurationLoader;
use App\RequestHandler\Request;
use App\RequestHandler\RequestHandler;

define('ROOT_PATH', __DIR__ . '/../');
define('CONFIG_PATH', ROOT_PATH . 'config/');
define('SRC_PATH', ROOT_PATH . 'src/');

require_once ROOT_PATH . 'include/global-functions.php';

$dotenv = new Rfussien\Dotenv\Loader(__DIR__);
$dotenv->load();

$configurationLoader = new ConfigurationLoader();
$configurationLoader->load();

$_SERVER['APP_ENV'] = Configuration::get(Configuration::APP_ENV);
$_SERVER['APP_DEBUG'] = Configuration::get(Configuration::APP_DEBUG);

if ($_SERVER['APP_DEBUG']) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

$request = new Request();
$requestHandler = new RequestHandler();
$requestHandler->handle($request);