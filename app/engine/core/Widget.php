<?php

namespace CloudStore\App\Engine\Core;

use CloudStore\App\Engine\Config\Config;
use CloudStore\CloudStore;

/**
 *
 * Loading widgets.
 *
 * All widgets have handler and view.
 * You don't need to edit widgets in engine/widgets path as this is built-in widgets.
 * But if you want to create own, just put your widget file into includes/widgets path.
 * Then write its name in the config.
 *
 * Use $this->widgets->widget_name->method ( $param ) to call it from template.
 *
 */

/**
 * Class Widget
 * @package CloudStore\App\Engine\Core
 */
class Widget
{
    /**
     * @var array
     */
    protected $styles = [];

    /**
     * @var array
     */
    protected $scripts = [];

    /**
     * @var Widget
     */
    protected $parent;

    /**
     * @var array
     */
    protected $staticFiles = [];

    /**
     * @var bool
     */
    protected $cacheOutput;

    /**
     * Widget constructor.
     * @param Widget|null $widget
     */
    public function __construct(Widget $widget = null)
    {
        $this->parent = $widget;
        $this->setStaticFiles();
    }

    /**
     * @return string
     * This function should be extended
     */
    public function getWidget(): string {
        return false;
    }

    /**
     * Should be called only on parent class
     */
    public function loadWidgets()
    {
        $this->createConstants();

        if (!is_callable([$this, 'setWidget'])) {
            return;
        }

        // step 1: get list of registered widgets
        $widgets = Config::$config['widgets'];

        // step 2: list it and check whether file exists or not
        foreach ($widgets as $key => $widget) {
            $widgetFilePath = WIDGETS_PATH . $widget . '.php';
            if (file_exists($widgetFilePath)) {
                // if exists just set it, we don't need to require it because of autoload
                $this->setWidget($widget);
            }
        }
    }

    /**
     * @param string $widgetName
     * @param string $namespace
     * @return bool
     */
    private function setWidget(string $widgetName, string $namespace = NAMESPACE_WIDGETS): bool
    {
        // I think it is good when property name starts with lowercase
        $widgetProperty = lcfirst($widgetName);

        if (isset($this->$widgetProperty)) {
            return false;
        }

        $widgetName = $namespace . $widgetName;

        $this->$widgetProperty = new $widgetName($this);

        return true;
    }

    /**
     * @param string $templateName
     * @param array $data
     * @return string
     * Note that this function should be called ONLY inside of templates, or inside of function that called inside of templates
     */
    public function render(string $templateName, array $data = array()): string
    {
        // create variables from array
        if ($data) {
            foreach ($data as $key => $value) {
                $$key = $value;
            }
        }

        $widgetFilePath = WIDGET_TEMPLATES_PATH . $templateName . '.php';
        if (file_exists($widgetFilePath)) {
            CloudStore::$app->system->buffer->createBuffer();
            require_once $widgetFilePath;
            return CloudStore::$app->system->buffer->returnBuffer();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getStyles(): string
    {
        foreach ($this->styles as $key => $style) {
            $this->styles[$key] = "<link rel='stylesheet' type='text/css' href='$style'>";
        }
        return implode("\n\r", $this->styles);
    }

    /**
     * @return string
     */
    public function getScripts()
    {
        foreach ($this->scripts as $key => $script) {
            $this->scripts[$key] = "<script type='text/javascript' src='$script'></script>";
        }
        return implode("\n\r", $this->scripts);
    }

    /**
     * @param string $style
     * @return bool
     * The main idea is provide ability to override associated with widget js and css files for customization in each theme
     * If there are no suck files than use default
     */
    public function setStyle(string $style): bool
    {
        if (!$this->parent) {
            return false;
        }

        if (strpos($style, '.css') === false) {
            $style .= '.css';
        }
        // check theme files
        $path = 'css/' . $style;
        $filePath = THEME_STATIC . $path;
        $urlPath = THEME_STATIC_URL . $path;
        if (file_exists($filePath)) {
            $this->parent->styles[] = $urlPath;
            return true;
        } else {
            // use common directory
            $path = 'widgets/' . $path;
            $filePath = COMMON . $path;
            $urlPath = COMMON_URL . $path;
            if (file_exists($filePath)) {
                $this->parent->styles[] = $urlPath;
                return true;
            }
            return false;
        }
    }

    /**
     * @param string $script
     * @return bool
     * The same as for setStyle()
     * todo these two function almost identical, implode them to single function
     */
    public function setScript(string $script)
    {
        if (!$this->parent) {
            return false;
        }
        if (strpos($script, '.js') === false) {
            $script .= '.js';
        }

        $path = 'js/' . $script;
        $filePath = THEME_STATIC . $path;
        $urlPath = THEME_STATIC_URL . $path;
        if (file_exists($filePath)) {
            $this->parent->scripts[] = $urlPath;
            return true;
        } else {
            // use common directory
            $path = 'widgets/' . $path;
            $filePath = COMMON . $path;
            $urlPath = COMMON_URL . $path;
            if (file_exists($filePath)) {
                $this->parent->scripts[] = $urlPath;
                return true;
            }
            return false;
        }
    }

    private function createConstants()
    {
        if (!defined('MVC_PATH')) {
            CloudStore::$app->exit('MVC_PATH is not defined.');
        }
        if (defined('WIDGET_TEMPLATES_PATH')) {
            return;
        }

        define('WIDGET_TEMPLATES_PATH', MVC_PATH . 'views/widgets/');
        define('WIDGETS_PATH', MVC_PATH . 'widgets/');
        define('NAMESPACE_WIDGETS', NAMESPACE_MVC_ROOT . 'Widgets\\');
    }

    protected function setStaticFiles()
    {
        if (!empty($this->staticFiles['js'])) {
            foreach ($this->staticFiles['js'] as $key => $script) {
                $this->setScript($script);
            }
        }
        if (!empty($this->staticFiles['css'])) {
            foreach ($this->staticFiles['css'] as $key => $style) {
                $this->setStyle($style);
            }
        }
    }
}
