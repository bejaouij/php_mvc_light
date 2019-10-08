<?php
    namespace App\Controllers;

    use App\Interfaces\Routing\Switchman;
    use App\Helpers\Core\Route_Manager\RouteManager;
    use App\Helpers\Core\Config_Manager\ConfigManager;

    abstract class Controller implements Switchman
    {
        static function redirect(string $route, bool $useName = true) : void {
            header('Location: ' . ConfigManager::getAppConfig('root_url') . ($useName ? RouteManager::getRouteURI($route) : $route));
        }
    }