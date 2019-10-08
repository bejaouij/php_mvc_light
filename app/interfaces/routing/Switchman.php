<?php
    namespace App\Interfaces\Routing;

    interface Switchman
    {
        static function redirect(string $route, bool $useName = true) : void;
    }