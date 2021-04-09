<?php

namespace Jet\App\Engine\Core;

use Jet\App\Engine\Config\Config;
use Jet\App\Engine\Interfaces\JSONOutput;
use Jet\App\Engine\Interfaces\MessageBox;
use Jet\App\Engine\Interfaces\ViewResponse;
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
     * @description string containing generated html
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
     * @var bool
     */
    private $includeLayout = true;
    /**
     * @var string
     */
    private $forcedTemplateName;
    /**
     * @var array
     */
    private $messageBoxStyles = [
        'error',
        'success',
        'warning',
        'danger',
        'info'
    ];
    /**
     * @var array
     */
    private $JSONOutput = [
        'status' => false,
        'message_box' => [
            'style' => 'info',
            'text' => ''
        ],
        'action' => '',
        'data' => []
    ];

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
     * @param bool $status
     * @param string $action
     * @param MessageBox|null $messageBox
     * @return ViewResponse
     */
    public function render(string $templateName = "default", array $data = [], bool $status = true, string $action = '', MessageBox $messageBox = null): ViewResponse
    {
        if ($this->isSPA()) { // proceed as SPA
            return $this->json($status, $data, $action, $messageBox);
        } else { // proceed as MPA
            return $this->html($templateName, $data);
        }
    }

    /**
     * @param string $templateName
     * @param array $data
     * @return ViewResponse
     */
    public function html(string $templateName = "default", array $data = []): ViewResponse
    {
        $response = new ViewResponse($this->isSPA());
        if ($this->forcedTemplateName) {
            $templateName = $this->forcedTemplateName;
        }

        // This variable will be echoed in layout
        $this->view = $this->returnHTMLOutput($templateName, $data);
        if ($this->includeLayout) {
            // Create compressed buffer
            $this->buffer->createBuffer();
            $filePath = VIEW_PATH . 'layout/' . $this->layout . '.php';
            if (file_exists($filePath)) {
                require_once $filePath;
            } else {
                PHPJet::$app->exit('template not found');
            }

            $this->view = $this->buffer->returnBuffer();
        }

        $response->response = $this->view;
        return $response;
    }

    /**
     * @param array $data
     * @param bool $status
     * @param string $action
     * @param MessageBox|null $messageBox
     * @return ViewResponse
     */
    public function json(bool $status = true, array $data = [], string $action = '', MessageBox $messageBox = null): ViewResponse
    {
        $response = new ViewResponse($this->isSPA());
        $response->response = $this->returnJsonOutput($status, $data, $action, $messageBox);
        return $response;
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
     * @param string $templateName
     * @param array $data
     * @return string
     */
    private function returnHTMLOutput(string $templateName = "default", array $data = array()): string
    {
        $controllerName = PHPJet::$app->router->getControllerName(true);
        $templatePath = VIEW_PATH . "pages/{$controllerName}/{$templateName}.php";
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
        include $templatePath;

        // Return html
        return $this->buffer->returnBuffer();
    }

    /**
     * @param bool $status
     * @param array $data
     * @param string $action
     * @param MessageBox|null $messageBox
     * @return string
     */
    private function returnJsonOutput(bool $status = false, array $data = [], string $action = '', MessageBox $messageBox = NULL): string
    {
        // do i really need it? it'd be easier to just return an array
        // todo think about it
        $jsonOutput = new JSONOutput();
        $jsonOutput->status = $status;
        $jsonOutput->data = $data;
        $jsonOutput->action = $action;
        if ($messageBox) {
            $jsonOutput->messageBox = $messageBox;
        }
        return $jsonOutput->returnJsonOutput();
    }

    private function loadTheme(): void
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

    private function createConstants(): void
    {
        // Create template constants
        // In some cases, for instance when using modules, View can be created several times
        // But we need to define all constants once
        // It's temporary, so now i don't care about better realization
        if (defined("THEME_LAYOUT")) {
            return;
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
    }

    /**
     * @return bool
     */
    public function isSPA(): bool
    {
        return Config::$availableThemes[MVC_SECTOR][$this->theme]['SPA'];
    }

    /**
     * These four functions are created especially for PageBuilder
     * It is highly not recommended to use em outside PageBuilder
     */
    public function _pb__disableLayout()
    {
        $this->includeLayout = false;
    }

    public function _pb__enableLayout()
    {
        $this->includeLayout = true;
    }

    /**
     * @param string $forcedTemplateName
     */
    public function _pb__setForcedTemplateName(string $forcedTemplateName)
    {
        $this->forcedTemplateName = $forcedTemplateName;
    }

    public function _pb__unsetForcedTemplateName()
    {
        $this->forcedTemplateName = null;
    }
}
