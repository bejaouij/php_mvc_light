<?php
    namespace App\Helpers\Core\Config_Manager;

    use \InvalidArgumentException;

    define('CORE_HELPERS_DEPTH_LEVEL', 3);

    final class ConfigManager
    {
        static private $configTypes = [
            'app' => 'app',
            'database' => 'database'
        ];

        private function __construct() {}

        static private function getConfig($configType, $configName, $configStepsSeparator = '.') {
            if(!in_array($configType, self::$configTypes)) {
                throw new InvalidArgumentException('The configuration type \'' . $configType . '\' does not exist.');
            }

            $config = require(dirname(__DIR__, CORE_HELPERS_DEPTH_LEVEL) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . self::$configTypes[$configType] . '.php');

            if(is_null($configName)) {
                return $config;
            }

            foreach(explode($configStepsSeparator, $configName) as $configStep) {
                if(!is_array($config) || !key_exists($configStep, $config)) {
                    throw new InvalidArgumentException('The configuration \'' . $configName . '\' dos not exist in the \'' . $configType . '\' scope.');
                }

                $config = $config[$configStep];
            }

            return $config;
        }

        static public function getAppConfig($configName = NULL, $configStepsSeparator = '.') {
            return self::getConfig(self::$configTypes['app'], $configName, $configStepsSeparator);
        }

        static public function getDatabaseConfig($configName = NULL, $configStepsSeparator = '.') {
            return self::getConfig(self::$configTypes['database'], $configName, $configStepsSeparator);
        }
    }