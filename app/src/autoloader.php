<?php
    spl_autoload_register(function($class) {
        $appConfig = require(__DIR__ . '/../config/app.php');

        $classNamespace = substr($class, 0, (strrpos($class, '\\') + 1 ));
        $classname = substr($class, (strrpos($class, '\\') + 1));
        $classpath = $appConfig['root_dir'] . strtolower(str_replace('\\', '/', $classNamespace)) . $classname . '.php';

        require($classpath);
    });