<?php

namespace Config;

class Config
{
    private const FILE = '../config/main.php';
    private static $config;

    public static function setConfig($url, $scratch = [], $header = [], $tail = '')
    {
        self::$config = require(self::FILE);
    }

    public static function getConfig()
    {
        return self::$config;
    }

    public static function set($name, $value)
    {
        self::$config[$name] = $value;
    }

    public static function get($name)
    {
        return self::$config[$name];
    }

}

