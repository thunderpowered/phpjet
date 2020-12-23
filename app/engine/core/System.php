<?php

namespace CloudStore\App\Engine\Core;

use CloudStore\App\Engine\Components;
use CloudStore\App\Engine\Config;
use CloudStore\App\Engine\System\Buffer;
use CloudStore\App\Engine\System\Mail;
use CloudStore\App\Engine\System\Request;
use CloudStore\App\Engine\System\Settings;
use CloudStore\App\Engine\System\Token;
use CloudStore\App\Engine\System\Tracker;
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
     * @var Tracker
     */
    public $tracker;

    /**
     * @var array
     */
    private $versionStages = [
        'Alpha',
        'Beta',
        'Pre-release',
        'Stable'
    ];

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
        $this->tracker = new Tracker();

        $this->controllerActive = false;
    }

    /**
     * @param string $rootURL
     * @return string
     */
    public function getMVCPath(string $rootURL): string
    {
        return MVC . Config\Config::$urlRules[$rootURL] . '/';
    }

    /**
     * @param string $setting_name
     * @return bool|mixed
     * @deprecated
     */
    public static function getSettingsSingle(string $setting_name)
    {
        return CloudStore::$app->store->loadOne("settings", ["settings_name" => $setting_name], false);
    }

    /**
     * @param string $section
     * @return bool|mixed
     * @deprecated
     */
    public static function getSettingsSection(string $section)
    {
        return CloudStore::$app->store->loadOne("settings", ["settings_section" => $section]);
    }

    /**
     * @return array|bool
     * @deprecated
     */
    public static function getAllSettings()
    {
        return CloudStore::$app->store->load("settings");
    }

    /**
     * @param string $controllerName
     * @param string $rootURL
     * @return bool
     */
    public function isControllerActive(string $controllerName, string $rootURL): bool
    {
        if ($this->controllerActive) {
            return true;
        }

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
        $version = ENGINE_VER_GLOBAL . '.' . ENGINE_VER_FUNC . '.' . ENGINE_VER_DEV . ' ' . $this->versionStages[ENGINE_VER_STAGE];
        if ($includeName) {
            $version = $version . ' (' . ENGINE_VER_RELEASE_NAME . ')';
        }

        return $version;
    }
}
