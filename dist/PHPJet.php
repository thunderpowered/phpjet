<?php

namespace Jet;

use Jet\App\App;
use Jet\App\Engine\Exceptions\CoreException;

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

    /**
     * @param string $mode
     * @throws CoreException
     */
    public static function init(string $mode)
    {
        self::defineConstants();
        self::$app = new App($mode);
    }

    private static function defineConstants()
    {
        define("NAMESPACE_ROOT", "Jet");
        define("NAMESPACE_APP", NAMESPACE_ROOT . "\App\\");
        define("NAMESPACE_ROOT_CLIENT", NAMESPACE_APP . "MVC\Client");
        define("NAMESPACE_ROOT_ADMIN", NAMESPACE_APP . "MVC\Admin");
        define("NAMESPACE_ROOT_COMMON", NAMESPACE_APP . "MVC\Common");
    }
}