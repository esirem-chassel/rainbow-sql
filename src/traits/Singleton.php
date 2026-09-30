<?php
namespace App\Rainbow\traits;

trait Singleton {
    protected static $instance = null;
    public static function getInstance(): static {
        if(null === static::$instance) {
            $cls = get_called_class();
            static::$instance = new $cls();
        }
        return static::$instance;
    }

    protected function __construct() {}
}
