<?php

namespace Jet;

use Jet\App\App;

/**
 * Class PHPJet
 * @package Jet\App\Engine
 */
class PHPJet
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
        define("NAMESPACE_ROOT", "Jet");
        define("NAMESPACE_ROOT_CLIENT", NAMESPACE_ROOT . "\App\MVC\Client");
        define("NAMESPACE_ROOT_ADMIN", NAMESPACE_ROOT . "\App\MVC\Admin");
        define("NAMESPACE_ROOT_COMMON", NAMESPACE_ROOT . "\App\MVC\Common");
    }
}