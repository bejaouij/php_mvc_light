<?php
    namespace App\Helpers\Core\Route_Manager;

    use App\Controllers\Controller;
    use App\Exceptions\RouteException;
    use App\Helpers\Core\Config_Manager\ConfigManager;

    defined('CONTROLLERS_NAMESPACE') || define('CONTROLLERS_NAMESPACE', 'App\Controllers\\');

    abstract class RouteManager
    {
        private static function getRoutesData() : array {
            return require(self::getRoutesDir() . DIRECTORY_SEPARATOR . 'route.php');
        }

        private static function getRouteDataByName(string $route) : array {
            if(empty($_SERVER['REQUEST_METHOD'])) {
                throw new RouteException('No verb provided for the request.');
            }

            $verb = $_SERVER['REQUEST_METHOD'];
            $routes = self::getRoutesData();

            if(!isset($routes[$verb])) {
                throw new RouteException('Invalid verb \'' . $verb . '\' in routes table.');
            }
            if(!isset($routes[$verb][$route])) {
                throw new RouteException('Invalid route \'' . $verb . ':' . $route . '\' in routes table.');
            }

            return $routes[$verb][$route];
        }

        public static function getRouteURI(string $route) : string {
            return self::getRouteDataByName($route)['uri'];
        }

        private static function getRouteDataByURI(string $URI) : array {
            if(empty($_SERVER['REQUEST_METHOD'])) {
                throw new RouteException('No verb provided for the request.');
            }

            $verb = $_SERVER['REQUEST_METHOD'];
            $routes = self::getRoutesData();

            if(!isset($routes[$verb])) {
                throw new RouteException('Invalid verb \'' . $verb . '\' in routes table.');
            }

            $rootURL = ConfigManager::getAppConfig('root_url');

            foreach($routes[$verb] as $route) {
                if($URI === ($rootURL . $route['uri'])) {
                    return $route;
                }
            }

            throw new RouteException('Invalid route \'' . $verb . ':' . $URI . '\' in routes table.');
        }

        public static function getRoutesDir() : string {
            return ConfigManager::getAppConfig('root_dir') . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'routes';
        }

        public static function action(string $route, $data = NULL, $useName = true) : void {
            try {
                $action = $useName ? self::getRouteDataByName($route)['action'] : self::getRouteDataByURI($route)['action'];
            } catch(RouteException $e) {
                echo $e->getMessage();
                Controller::redirect('404');
            }

            $controller = CONTROLLERS_NAMESPACE . substr($action, '0', strpos($action, '@'));
            $method = substr($action, (strpos($action, '@') + 1));

            (new $controller())->$method($data);
        }
    }