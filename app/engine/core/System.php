<?php

namespace Jet\App\Engine\Core;

use Jet\App\Engine\Config;
use Jet\App\Engine\System\Buffer;
use Jet\App\Engine\System\Mail;
use Jet\App\Engine\System\Request;
use Jet\App\Engine\System\Settings;
use Jet\App\Engine\System\Token;
use Jet\App\Engine\System\Tracker;
use Jet\PHPJet;

/**
 * Class System
 * @package Jet\App\Engine\Core
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
        $this->request = new Request();
        $this->token = new Token();
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
     * @return array
     * @throws \Exception
     * @deprecated
     */
    public static function getSettingsSingle(string $setting_name)
    {
        return PHPJet::$app->store->loadOne("settings", ["settings_name" => $setting_name], false);
    }

    /**
     * @param string $section
     * @return array
     * @throws \Exception
     * @deprecated
     */
    public static function getSettingsSection(string $section)
    {
        return PHPJet::$app->store->loadOne("settings", ["settings_section" => $section]);
    }

    /**
     * @return array
     * @throws \Exception
     * @deprecated
     */
    public static function getAllSettings()
    {
        return PHPJet::$app->store->load("settings");
    }

    /**
     * @param string $controllerName
     * @param string $MVCSector
     * @param bool $forceCheck
     * @return bool
     */
    public function isControllerActive(string $controllerName, string $MVCSector, bool $forceCheck = false): bool
    {
        if ($this->controllerActive && !$forceCheck) {
            return true;
        }

        $MVCSector = strtolower($MVCSector);
        $this->controllerActive = in_array($controllerName, Config\Config::$config['controllers'][$MVCSector]);
        return $this->controllerActive;
    }

    /**
     * @param bool $includeName
     * @param bool $includeTitle
     * @return string
     */
    public function getEngineVersion(bool $includeName = false, bool $includeTitle = true): string
    {
        // global.func.stage:dev
        $version = ENGINE_VER_GLOBAL . '.' . ENGINE_VER_FUNC . '.' . ENGINE_VER_DEV . ' ' . $this->versionStages[ENGINE_VER_STAGE];
        if ($includeName) {
            $version = $version . ' (' . ENGINE_VER_RELEASE_NAME . ')';
        }
        if ($includeTitle) {
            $version = 'Engine Version: ' . $version;
        }

        return $version;
    }
}
