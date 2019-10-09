<?php
    namespace App\Controllers;

    use App\View\View;
    use App\Interfaces\Routing\Switchman;
    use App\Helpers\Core\Route_Manager\RouteManager;
    use App\Helpers\Core\Config_Manager\ConfigManager;

    abstract class Controller implements Switchman
    {
        static function redirect(string $route, bool $useName = true) : void {
            header('Location: ' . ConfigManager::getAppConfig('root_url') . ($useName ? RouteManager::getRouteURI($route) : $route));
        }

        static protected function render(string $view, $data = NULL, bool $isStatic = false) : void {
            View::render($view, $data, $isStatic);
        }
    }