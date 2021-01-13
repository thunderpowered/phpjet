<?php

namespace Jet\App\Engine\Core;

use Jet\App\Engine\Config\Config;
use Jet\App\Engine\System\Buffer;
use Jet\PHPJet;

/**
 *
 * Main handler of View-component in MVC structure.
 * Most of view-constant are generating here.
 *
 * Use $this->view->render $view_name, $array_of_param) to generate View (you need to use it inside of controller).
 *
 * Use $this->method($param) from template to call View methods.
 *
 * You also can call controller. Just use $this->controller->method($param);
 */

/**
 * Class View
 * @package Jet\App\Engine\Core
 */
class View
{
    /**
     * @var Buffer
     */
    public $buffer;

    /**
     * @var null|Controller
     */
    private $controller;

    /**
     * @var Widget
     */
    private $widget;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $defaultTemplate = "main";

    /**
     * @var string
     */
    private $templatePrefix = "view_";

    /**
     * @var string
     */
    private $layout = "main";

    /**
     * @var string
     */
    private $view;
    /**
     * @var string
     */
    private $themeDefault = 'default';
    /**
     * @var string
     */
    private $theme;
    /**
     * @var string
     */
    private $themePath;

    /**
     * View constructor.
     * @param Controller|null $controller
     */
    public function __construct(Controller $controller)
    {
        $this->themePath = MVC_SECTOR . '/';
        $this->loadTheme();
        $this->createConstants();

        $this->controller = $controller;
        $this->widget = new Widget();
        $this->widget->setController($controller);

        $this->buffer = PHPJet::$app->system->buffer;
    }

    public function loadWidgets()
    {
        if ($this->widget) {
            $this->widget->loadWidgets();
        }
    }

    /**
     * @param string $templateName
     * @param array $data
     * @return string
     */
    public function render(string $templateName = "default", array $data = array()): string
    {
        // This variable will be echoed in layout
        $this->view = $this->returnHTMLOutput($templateName, $data);

        // Create compressed buffer
        $this->buffer->createBuffer();

        $filePath = VIEW_PATH . 'layout/' . $this->layout . '.php';
        if (file_exists($filePath)) {
            require_once $filePath;
        }

        return $this->buffer->returnBuffer();
    }

    /**
     * @param string $templateName
     * @param array $data
     * @return string
     */
    public function returnHTMLOutput(string $templateName = "default", array $data = array()): string
    {
        $templatePath = VIEW_PATH . 'pages/' . PHPJet::$app->router->getControllerName(true) . '/' . $templateName . '.php';
        if (!file_exists($templatePath)) {
            return "";
        }

        // Create buffer
        $this->buffer->createBuffer();

        // Create variables
        if ($data) {
            foreach ($data as $key => $value) {
                $$key = $value;
            }
        }

        // Include view
        require_once $templatePath;

        // Return html
        return $this->buffer->returnBuffer();
    }

    /**
     * @param string $partName
     * @return string
     */
    public function includePart(string $partName): string
    {
        // Modules are not available at the moment
        $partPath = VIEW_PATH . 'parts/' . $partName . '.php';
        if (file_exists($partPath)) {
            // Nested buffer
            $this->buffer->createBuffer();
            require_once $partPath;
            return $this->buffer->returnBuffer();
        }

        return '';
    }

    /**
     * @param string $jsPath
     * @param bool $reload
     * @param bool $asPlainText
     * @return string
     */
    public function includeJS(string $jsPath, bool $reload = false, bool $asPlainText = false): string
    {
        $filePath = WEB . 'theme/' . $this->themePath . Config::$availableThemes[MVC_SECTOR][$this->theme]['static'] . '/' . $jsPath;
        $fileURL = THEME_STATIC_URL . $jsPath;
        if (Config::$dev['debug'] || $reload) {
            $fileURL .= "?t=" . time();
        }

        if (file_exists($filePath)) {
            if ($asPlainText) {
                $content = file_get_contents($filePath);
                return "<script type='text/javascript'>" . $content . "</script>";
            } else {
                return "<script type='text/javascript' src=\"" . $fileURL . "\"></script>";
            }
        } else {
            return '';
        }
    }

    /**
     * @param string $jsPath
     * @param bool $reload
     * @param bool $asPlainText
     * @return string
     */
    public function includeCommonJS(string $jsPath, bool $reload = false, bool $asPlainText = false): string
    {
        $filePath = WEB . 'theme/common/' . $jsPath;
        $fileURL = THEME_COMMON_URL . $jsPath;
        if (Config::$dev['debug'] || $reload) {
            $fileURL .= "?t=" . time();
        }

        if (file_exists($filePath)) {
            if ($asPlainText) {
                $content = file_get_contents($filePath);
                return "<script type='text/javascript'>" . $content . "</script>";
            } else {
                return "<script type='text/javascript' src=\"" . $fileURL . "\"></script>";
            }
        } else {
            return '';
        }
    }

    /**
     * @param string $cssPath
     * @param bool reload
     * @return bool
     */
    public function includeCSS(string $cssPath, bool $reload = false)
    {
        $filePath = WEB . 'theme/' . $this->themePath . Config::$availableThemes[MVC_SECTOR][$this->theme]['static'] . '/' . $cssPath;
        $fileURL = THEME_STATIC_URL . $cssPath;
        if (Config::$dev['debug'] || $reload) {
            $fileURL .= "?t=" . time();
        }

        if (file_exists($filePath)) {
            return "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $fileURL . "\">";
        } else {
            return '';
        }
    }

    /**
     * @param string $cssPath
     * @param bool $reload
     * @return string
     */
    public function includeCommonCSS(string $cssPath, bool $reload = false): string
    {
        $filePath = WEB . 'theme/common/' . $cssPath;
        $fileURL = THEME_COMMON_URL . $cssPath;
        if (Config::$dev['debug'] || $reload) {
            $fileURL .= "?t=" . time();
        }

        if (file_exists($filePath)) {
            return "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $fileURL . "\">";
        } else {
            return '';
        }
    }

    /**
     * @param string $file
     * @deprecated
     */
    public static function includeCommon(string $file)
    {
    }

    /**
     * @return string
     */
    public function getTemplateName(): string
    {
        $routes = PHPJet::$app->router->getRoute();

        if (!empty($routes[1])) {
            $this->defaultTemplate = strtolower(PHPJet::$app->tool->utils->removeSpecialChars($routes[1]));
        }
        return $this->templatePrefix . $this->defaultTemplate;
    }

    /**
     * @param string $layout
     */
    public function setLayout(string $layout)
    {
        $this->layout = $layout;
    }

    /**
     * @param bool $success
     * @param array $data
     * @return string
     */
    public function returnJsonOutput(bool $success = false, array $data = []): string
    {
        $data['success'] = $success;
        return json_encode($data);
    }

    private function loadTheme()
    {
        $theme = PHPJet::$app->system->settings->getContext('theme');
        if (!$theme) {
            PHPJet::$app->exit('No theme to load. Open PHPJet Administrator Panel and select the theme.');
        }

        if (array_key_exists($theme, Config::$availableThemes[MVC_SECTOR])) {
            $this->theme = $theme;
        } else {
            $this->theme = $this->themeDefault;
        }
    }

    /**
     * @return bool
     */
    private function createConstants(): bool
    {
        // Create template constants
        // In some cases, for instance when using modules, View can be created several times
        // But we need to define all constants once
        // It's temporary, so now i don't care about better realization
        if (defined("THEME_LAYOUT")) {
            return false;
        }

        $host = PHPJet::$app->router->getHost();

        // Creating constants for HTML-templates
        define("THEME_LAYOUT", Config::$availableThemes[MVC_SECTOR][$this->theme]['layout'] . '/');
        define("THEME_PARTS", Config::$availableThemes[MVC_SECTOR][$this->theme]['parts'] . '/');
        define("THEME_VIEWS", Config::$availableThemes[MVC_SECTOR][$this->theme]['views'] . '/');
        define("THEME_MAIL", Config::$availableThemes[MVC_SECTOR][$this->theme]['mail'] . '/');
        define("THEME", Config::$availableThemes[MVC_SECTOR][$this->theme]['layout'] . '/');

        // Creating constant for "static" directory
        define('THEME_COMMON_URL', $host . '/theme/common/');
        define("THEME_STATIC_URL", $host . '/theme/' . $this->themePath . Config::$availableThemes[MVC_SECTOR][$this->theme]['static'] . '/');
        define("THEME_STATIC", WEB . 'theme/' . $this->themePath . Config::$availableThemes[MVC_SECTOR][$this->theme]['static'] . '/');
        define("COMMON", WEB . 'common/');
        define("COMMON_URL", $host . '/common/');
        define("VIEW_PATH", MVC_PATH . "views/theme/" . THEME);

        return true;
    }
}
