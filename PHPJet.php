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
    }
}