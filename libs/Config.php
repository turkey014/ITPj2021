<?php // Config.php
declare(strict_types=1);

if(false === defined('BASEPATH')) {
    define('BASEPATH', realpath(__DIR__ . '/..'));
}

class Config {
    public static function get(string $name, $default=null){
        
        if(null === static::$config){
            $env = require(BASEPATH . '/environment.config');
            
            static::$config = $env;
        }
        
        return static::$config[$name] ?? $default;
    }
    
    private static $config = null;
}