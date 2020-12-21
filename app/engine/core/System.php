<?php

namespace CloudStore\App\Engine\Core;

use CloudStore\App\Engine\Components;
use CloudStore\App\Engine\Config;
use CloudStore\App\Engine\System\Buffer;
use CloudStore\App\Engine\System\Mail;
use CloudStore\App\Engine\System\Request;
use CloudStore\App\Engine\System\Settings;
use CloudStore\App\Engine\System\Token;
use CloudStore\CloudStore;

/**
 * Class System
 * @package CloudStore\App\Engine\Core
 */
class System
{
    /**
     * @var Widget
     */
    public $widgets;

    /**
     * @var bool
     */
    private $controllerActive;

    /**
     * @var Buffer
     */
    public $buffer;

    /**
     * @var Mail
     */
    public $mail;

    /**
     * @var Token
     */
    public $token;

    /**
     * @var Request
     */
    public $request;

    /**
     * @var Settings
     */
    public $settings;

    /**
     * System constructor.
     */
    public function __construct()
    {
        $this->buffer = new Buffer();
        $this->mail = new Mail();
        $this->token = new Token();
        $this->request = new Request();
        $this->settings = new Settings();

        $this->controllerActive = false;
    }

    /**
     * @param string $rootURL
     * @return string
     */
    public function getMVCPath(string $rootURL): string
    {
        return MVC . Config\Config::$urlRules[$rootURL] . '/';

//        // deprecated
//        // step 0: get the url
//        $routePart = CloudStore::$app->router->getRoutePart(1);
//
//        // step 1: proceed url rules
//        $urlRules = Config\Config::$urlRules;
//        if ($routePart && !empty($urlRules[$routePart])) {
//            return MVC . $urlRules[$routePart];
//        }
//        return MVC . $urlRules[''];
//
//        // modules are not available at the moment
//        if (!isset(Config\Config::$config["module"])) {
//            return ENGINE;
//        }
//
//        $controller = "Controller" . CloudStore::$app->router->getControllerName(false);
//        $controller_file = MVC . 'modules/' . Config\Config::$config["module"] . '/controllers/' . $controller . '.php';
//
//        return file_exists($controller_file) ? MVC . 'modules/' . Config\Config::$config["module"] . '/' : ENGINE;
    }

    /**
     * @param string $setting_name
     * @return bool|mixed
     */
    public static function getSettingsSingle(string $setting_name)
    {
        return CloudStore::$app->store->loadOne("settings", ["settings_name" => $setting_name], false);
    }

    /**
     * @param string $section
     * @return bool|mixed
     */
    public static function getSettingsSection(string $section)
    {
        return CloudStore::$app->store->loadOne("settings", ["settings_section" => $section]);
    }

    /**
     * @return array|bool
     */
    public static function getAllSettings()
    {
        return CloudStore::$app->store->load("settings");
    }

    /**
     * @param string $controllerName
     * @return bool
     */
    public function isControllerActive(string $controllerName, string $rootURL): bool
    {
        if ($this->controllerActive) {
            return true;
        }

        // First of all we need to check module
        // Because module can has different list of controllers
        $directory = self::getMVCPath($rootURL);

        // If module
        // Modules are not available at the moment!
        if ($directory !== ENGINE AND file_exists($directory . "config.php") && false) {
            require_once $directory . "config.php";
            if (!empty($controllers) AND is_array($controllers)) {
                foreach ($controllers as $controller) {
                    if (!in_array($controller, Config\Config::$config['controllers'])) {
                        Config\Config::$config['controllers'][] = $controller;
                    }
                }
            }
        }

        // Check controllers
        $this->controllerActive = in_array($controllerName, Config\Config::$config['controllers']);
        return $this->controllerActive;
    }

    /**
     * @param bool $includeName
     * @return string
     */
    public function getEngineVersion(bool $includeName = false): string
    {
        // global.func.stage:dev
        $version = ENGINE_VER_GLOBAL . '.' . ENGINE_VER_FUNC . '.' . ENGINE_VER_STAGE . ':' . ENGINE_VER_DEV;
        if ($includeName) {
            $version = $version . ' (' . ENGINE_VER_RELEASE_NAME . ')';
        }

        return $version;
    }
}
