<?php

namespace CloudStore\App\Engine\Core;

use CloudStore\App\Engine\Config\Config;
use CloudStore\App\Engine\System\Buffer;
use CloudStore\CloudStore;
use function GuzzleHttp\default_ca_bundle;

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
 * @package CloudStore\App\Engine\Core
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
     * View constructor.
     * @param Controller|null $controller
     */
    public function __construct(Controller $controller = null)
    {
        $this->createConstants();

        $this->controller = $controller;
        $this->widget = new Widget();

        $this->buffer = CloudStore::$app->system->buffer;
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

        // Including layout
        // Layout can be located out of module
        // Modules are not available, but will be
//        if (MVC_PATH !== ENGINE AND file_exists(VIEW_PATH . 'layout/' . THEME_LAYOUT . $this->layout . '.php')) {
        if (false) {
            // for module, temporarily disabled
            require_once VIEW_PATH . 'layout/' . THEME_LAYOUT . $this->layout . '.php';
        } else {
            require_once VIEW_PATH . 'layout/' . $this->layout . '.php';
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
        $templatePath = VIEW_PATH . 'pages/' . CloudStore::$app->router->getControllerName(true) . '/' . $templateName . '.php';
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
     * @return string
     */
    public static function includeJS(string $jsPath, $reload = true): string
    {
        if (file_exists(WEB . 'theme/' . Config::$theme['static'] . '/' . $jsPath)) {
            return "<script src=\"" . THEME_STATIC_URL . $jsPath . "?time=" . time() . "\"></script>";
        } else {
            return '';
        }
    }

    /**
     * @param string $cssPath
     * @param bool reload
     * @return bool
     */
    public static function includeCSS(string $cssPath, $reload = true)
    {
        if (file_exists(WEB . 'theme/' . Config::$theme['static'] . '/' . $cssPath)) {
            return "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . THEME_STATIC_URL . $cssPath . "?time=" . time() . "\">";
        } else {
            return '';
        }
    }

    /**
     * @param string $file
     */
    public static function includeCommon(string $file)
    {

        if (file_exists(WEB . $file)) {

            echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . Router::getHost() . "/$file\">";
        }
    }

    /**
     * @return bool
     */
    private static function createConstants(): bool
    {
        // Create template constants
        // In some cases, for instance when using modules, View can be created several times
        // But we need to define all constants once
        // It's temporary, so now i don't care about better realization
        if (defined("THEME_LAYOUT")) {
            return false;
        }

        $host = CloudStore::$app->router->getHost();

        //Creating constants
        // todo: think about the way it used to be. seems like it was a bit effective than now
        define("THEME_LAYOUT", Config::$theme['layout'] . '/');
        define("THEME_PARTS", Config::$theme['parts'] . '/');
        define("THEME_VIEWS", Config::$theme['views'] . '/');
        define("THEME_MAIL", Config::$theme['mail'] . '/');

        define("THEME", Config::$theme['layout'] . '/');
        //Creating constant for "static" directory
        define("THEME_STATIC_URL", $host . '/theme/' . Config::$theme['static'] . '/');
        define("THEME_STATIC", WEB . 'theme/' . Config::$theme['static'] . '/');
        define("COMMON", WEB . 'common/');
        define("COMMON_URL", $host . '/common/');
        define("VIEW_PATH", MVC_PATH . "views/theme/" . THEME);

        return true;
    }

    /**
     * @return string
     */
    public function getTemplateName(): string
    {
        $routes = CloudStore::$app->router->getRoute();

        if (!empty($routes[1])) {
            $this->defaultTemplate = strtolower(CloudStore::$app->tool->utils->removeSpecialChars($routes[1]));
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
}
