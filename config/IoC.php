<?php

namespace Config;

class IoC
{

    private const FILE = '../config/services.php';
    private static $registry = array();

    /**
     * Add a new resolver to the registry array.
     * @return void
     */
    public static function setRegister()
    {
        self::$registry = require_once(self::FILE);
    }

    /**
     * Create the instance
     * @param string $service
     * @param string $name The id
     * @return object
     */
    public static function resolve($service, $name)
    {
        if (static::registered($service, $name)) {
            $name = self::$registry[$service][$name];
            return new $name;
        }
    }

    /**
     * Determine whether the id is registered
     * @param string $service
     * @param string $name The id
     * @return bool Whether to id exists or not
     */
    private static function registered($service, $name)
    {
        if (!array_key_exists($service, self::$registry) or !array_key_exists($name, self::$registry[$service])) {
            exit('Nothing service or registered with that name.');
        }
        return true;
    }
}