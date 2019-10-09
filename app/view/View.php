<?php
    namespace App\View;

    use App\Exceptions\InvalidViewPathException;

    defined('VIEW_MANAGER_DEPTH_LEVEL') || define('VIEW_MANAGER_DEPTH_LEVEL', 2);

    class View
    {
        static public function render(string $view, $data = NULL, bool $isStatic = false) {
            if(empty($view)) {
                throw new InvalidViewPathException('View \'' . $view . '\' does not exist.');
            }

            require(self::getPath($view, $isStatic));
        }

        static public function getPublicDir() {
            return dirname(__DIR__, VIEW_MANAGER_DEPTH_LEVEL) . DIRECTORY_SEPARATOR . 'public';
        }

        static private function getPath(string $view, bool $isStatic = false) : string {
            return self::getPublicDir() . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . self::parse($view) . '.' . (!$isStatic ? 'php' : 'html');
        }

        static private function parse(string $view) : string {
            return str_replace('.', DIRECTORY_SEPARATOR, $view);
        }
    }