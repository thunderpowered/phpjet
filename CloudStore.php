<?php

namespace CloudStore;

use CloudStore\App\App;

/**
 * Class CloudStore
 * @package CloudStore\App\Engine
 */
class CloudStore
{
    /**
     * @var App
     */
    public static $app;

    public static function init()
    {
        self::defineConstants();
        self::$app = new App();
    }

    private static function defineConstants()
    {
        define("NAMESPACE_ROOT", "CloudStore");
    }
}