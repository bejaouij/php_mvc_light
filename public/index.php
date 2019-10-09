<?php
    use App\Helpers\Core\Route_Manager\RouteManager;
    use App\Helpers\Core\Config_Manager\ConfigManager;

    defined('INDEX_DEPTH_LEVEL') || define('INDEX_DEPTH_LEVEL', 1);

    require(dirname(__DIR__, INDEX_DEPTH_LEVEL) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'autoloader.php');

    if(ConfigManager::getAppConfig('display_errors')) {
        error_reporting(E_ALL);
        ini_set('display_errors', 'On');
    }

    RouteManager::action($_SERVER['REQUEST_URI'], NULL, false);