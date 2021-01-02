<?php

namespace CloudStore\App\Engine\Core;

use CloudStore\App\Engine\Ajax\AjaxRouter;
use CloudStore\App\Engine\Config;
use CloudStore\App\MVC\Client\Controllers\ControllerPage;
use CloudStore\CloudStore;

/**
 * Class Router
 * @package CloudStore\App\Engine\Core
 */
class Router
{
    /**
     * @var string
     */
    public $rootURL = '';
    /**
     * @var string
     */
    public $MVCSector = '';
    /**
     * @var string
     */
    private $actionBasic = "actionBasic";
    /**
     * @var array
     */
    private $route = [];
    /**
     * @var array
     */
    private $delimiter = ["?", "."];
    /**
     * @var Controller
     */
    private $controller;
    /**
     * @var Model
     */
    private $model;
    /**
     * @var View
     */
    private $view;
    /**
     * @varstring
     */
    private $controllerName;
    /**
     * @var array
     */
    private $default = [
        'controller' => 'main',
        'model' => 'main'
    ];
    /**
     * @var array
     */
    private $httpErrorCodes = [
        '500' => 'Internal Server Error',
        '404' => 'Not Found'
    ];
    /**
     * @var int
     */
    private $defaultControllerRoutePart = 1;
    /**
     * @var int
     */
    private $defaultActionRoutePart = 2;
    /**
     * Router constructor.
     */
    public function __construct()
    {
        // There is nothing to see here, citizen...
    }

    /**
     * @return string
     */
    public function start(): string
    {
        // prepare root URL (should not be in constructor for some reason)
        $route = $this->getRoutePart(1);
        if ($route && !empty(Config\Config::$urlRules[$route])) {
            $this->rootURL = Config\Config::$urlRules[$route];

            // if route is set, controller and action shift to the right by one part
            $this->defaultActionRoutePart++;
            $this->defaultControllerRoutePart++;
        }
        $this->MVCSector = $this->rootURL ? $this->rootURL : Config\Config::$urlRules[''];

        define('MVC_SECTOR', $this->MVCSector);
        define('MVC_PATH', CloudStore::$app->system->getMVCPath($this->rootURL));
        define('NAMESPACE_MVC_ROOT', NAMESPACE_ROOT . "\App\MVC\\" . $this->MVCSector . "\\");

        if (Config\Config::$pageBuilder[MVC_SECTOR]['active']) {
            // Proceed with page builder
            $this->setControllerName('page');
            $this->controller = $this->getControllerObject();
        } else {
            // Default way
            // CloudStore has a list of controller that may be included and executed
            // List of controllers is in the config
            $controllerName = $this->getControllerName(true);
            if (!CloudStore::$app->system->isControllerActive($controllerName, $this->MVCSector)) {
                return $this->errorPage404();
            }

            // Getting and including controller object
            $this->controller = $this->getControllerObject();
        }

        // If there is no such controller
        if (!$this->controller) {
            return $this->errorPage404();
        }

        return $this->startAction($this->controller);
    }
    /**
     * @param $controller
     * @return string
     */
    public function startAction(Controller $controller): string
    {
        // Dynamically get action
        $action = $this->getAction(true);
        if ($action && method_exists($controller, $action)) {
            return $controller->$action();
        }

        // Or use basic action
        $action = $this->actionBasic;
        if (method_exists($controller, $action) && is_callable([$controller, $action])) {
            return $controller->$action();
        }

        return $this->errorPage404();
    }
    /**
     * @return string
     */
    public function blocked(): string
    {
        $view = $this->getViewObject(new Controller());
        $view->setLayout("block");
        $view->buffer->destroyBuffer();
        return $view->render();
    }

    /**
     * @param bool $forceRedirect and forget about anything else
     * @return string
     * Just a shortcut for errorPage()
     */
    public function errorPage404(bool $forceRedirect = false): string
    {
        return $this->errorPage('404', 'Not Found', '404', $forceRedirect);
    }

    /**
     * @param bool $forceRedirect
     * @return string
     * Just a shortcut for errorPage()
     */
    public function errorPage500(bool $forceRedirect = false): string
    {
        return $this->errorPage('500', 'Internal Server Error', 'error', $forceRedirect);
    }


    /**
     * @param string $code
     * @param string $message
     * @param string $layout
     * @param bool $forceRedirect
     * @return string
     */
    public function errorPage(string $code = '500', string $message = 'Internal Server Error', string $layout = 'error', bool $forceRedirect = false): string
    {
        if (!$message) {
            $message = $this->httpErrorCodes[$code] ?? null;
        }

        $view = $this->getViewObject(new Controller());
        $view->setLayout($layout);
        $view->buffer->destroyBuffer();

        header("HTTP/1.1 {$code} {$message}");
        $result = $view->render();
        if ($forceRedirect) {
            // it's not a redirect technically, it just stops any further actions
            echo $result;
            CloudStore::$app->exit();
        }

        return $result;
    }
    /**
     * @param int $part
     * @param bool $toLowerCase
     * @param bool $cutToDelimiter
     * @return string
     */
    public function getRoutePart(int $part = 1, bool $toLowerCase = true, bool $cutToDelimiter = true): string
    {
        $route = $this->getRoute(false);
        if (empty($route[$part])) {
            return "";
        }
        $part = $route[$part];

        // Using delimiters
        $delimiterPosition = CloudStore::$app->tool->utils->strpos($part, $this->delimiter);
        if ($delimiterPosition !== false && $cutToDelimiter) {
            $part = substr($part, 0, $delimiterPosition);
        }

        if ($toLowerCase) {
            $part = mb_strtolower($part);
        }

        return $part;
    }
    /**
     * @param bool $removeSpecialChars
     * @return array
     */
    public function getRoute(bool $removeSpecialChars = true): array
    {
        if ($this->route) {
            return $this->route;
        }

        $requestURI = CloudStore::$app->system->request->getSERVER('REQUEST_URI');
        $this->route = explode('/', $requestURI);

        if ($removeSpecialChars) {
            $this->route = CloudStore::$app->tool->utils->removeSpecialChars($this->route);
        }

        return $this->route;
    }
    /**
     * @param bool $cut
     * @return string
     */
    public function getLastRoutePart(bool $cut = true): string
    {
        $part = "";
        $route = $this->getRoute();
        if ($route) {
            $part = end($route);
        }

        $delimiterPosition = CloudStore::$app->tool->utils->strpos($part, $this->delimiter);
        if ($delimiterPosition !== false && $cut) {
            $part = substr($part, 0, $delimiterPosition);
        }

        return $part;
    }
    /**
     * @param string $string
     * @return string
     */
    public function cutRouteString(string $string): string
    {
        $delimiterPosition = CloudStore::$app->tool->utils->strpos($string, $this->delimiter);
        return substr($string, 0, $delimiterPosition);
    }
    /**
     * @return string
     */
    public function getDomain(): string
    {
        if (!empty(Config\Config::$config['domain'])) {
            return Config\Config::$config["domain"];
        }
        return CloudStore::$app->system->request->getSERVER('HTTP_HOST');
    }
    /**
     * @return bool
     */
    public function isHome()
    {
        // Simple and brilliantly
        return $this->getHost() . '/' === $this->getURL();
    }
    /**
     * @return string
     */
    public function getHost()
    {
        $https = CloudStore::$app->system->request->getSERVER('HTTPS');
        $scheme = "http://";
        if ($https) {
            $scheme = "https://";
        }
        return $scheme . $this->getDomain();
    }
    /**
     * @param bool $removeSpecialChars
     * @return string
     */
    public static function getRequestURI(bool $removeSpecialChars = true): string
    {
        $request = CloudStore::$app->system->request->getSERVER('REQUEST_URI');

        if ($removeSpecialChars) {
            return CloudStore::$app->tool->utils->removeSpecialChars($request);
        } else {
            return $request;
        }
    }
    /**
     * @return string
     * @deprecated
     */
    public function getURN(): string
    {
        return CloudStore::$app->system->request->getSERVER('REQUEST_URI');
    }
    /**
     * @param bool $addHost
     * @return string
     */
    public function getURL(bool $addHost = true): string
    {
        $url = CloudStore::$app->system->request->getSERVER('REQUEST_URI');
        if ($addHost) {
            $url = $this->getHost() . $url;
        }
        return $url;
    }
    /**
     * @return Controller
     * @todo use constant instead of explicitly set of namespaces
     */
    public function getControllerObject(): Controller
    {
        if ($this->controller) {
            return $this->controller;
        }
        /**
         * @var Controller $controller
         */
        $controller = null;

        // Create controller name
        $name = '';
        $partName = $this->getControllerName(false);
        if ($partName) {
            $name = 'Controller' . $partName;
        }

        // Namespace sector
        $MVCRoot = $this->rootURL ? ucfirst($this->rootURL) : ucfirst(Config\Config::$urlRules['']);
        // Create full controller name with namespace
        $Class = "\CloudStore\App\MVC\\$MVCRoot\Controllers\\" . $name;

        // Create object
        if (class_exists($Class)) {
            $controller = new $Class($name);
        } else {
            // If there is no such class
            CloudStore::$app->exit("Class '{$Class}' Not Found");
        }

        // Create and set view
        $view = $this->getViewObject($controller);
        $view->loadWidgets();
        $controller->setView($view);

        // Create and set model
        $model = $this->getModelObject();
        $controller->setModel($model);

        return $controller;
    }
    /**
     * @param bool $lowerCase
     * @return string
     */
    public function getControllerName(bool $lowerCase = true): string
    {
        if (!$this->controllerName) {
            $routePart = $this->getRoutePart($this->defaultControllerRoutePart, true);

            $this->controllerName = $this->default['controller'];
            if ($routePart) {
                $this->controllerName = $routePart;
            }
        }

        if (!$lowerCase) {
            $this->controllerName = ucfirst($this->controllerName);
        }

        return $this->controllerName;
    }

    /**
     * @param string $controllerName
     * @return bool
     */
    public function setControllerName(string $controllerName): bool
    {
        if (!CloudStore::$app->system->isControllerActive($controllerName, $this->MVCSector, true)) {
            return false;
        }

        $this->controllerName = $controllerName;
        return true;
    }
    /**
     * @return Model
     * @deprecated
     * It'd be better to set model explicitly, not like that. Because one controller can use multiple models.
     */
    public function getModelObject(): Model
    {
        if ($this->model) {
            return $this->model;
        }

        $name = $this->getRoutePart(1, true);
        if (!$name) {
            $name = $this->default['model'];
        }
        $name = ucfirst($name);
        $name = 'Model' . $name;

        $Class = '\CloudStore\App\Engine\Models\\' . $name;
        if (MVC_PATH !== ENGINE) {
            $Class = '\Site\Content\Models\\' . $name;
        }

        if (class_exists($Class)) {
            $this->model = new $Class($name);
        } else {
            $this->model = new Model();
        }

        return $this->model;
    }
    /**
     * @param Controller $controller
     * @return View
     * Creates special view object for this controller
     */
    public function getViewObject(Controller $controller): View
    {
        if (!$this->view) {
            $this->view = new View($controller);
        }

        return $this->view;
    }
    /**
     * @param bool $full
     * @param bool $cut
     * @return bool|mixed|string
     */
    public function getAction(bool $full = false, bool $cut = true): string
    {
        $action = $this->getRoutePart($this->defaultActionRoutePart, true, $cut);
        if ($action && $full) {
            $action = 'action' . ucfirst($action);
        }

        return $action;
    }
    /**
     * @param string $relativeURL
     * @return string
     * todo: implement method printProperURL()
     */
    public function printProperURL(string $relativeURL): string
    {
        return '';
    }
    /**
     * @param string $url
     * @param int $code
     */
    public function redirect(string $url, $code = 301): void
    {
        header("Location: " . $url, true, $code);
        CloudStore::$app->exit();
    }
    /**
     * @param string $url
     * @param int $code
     */
    public function internalRedirect(string $url, $code = 301): void
    {

    }
    public function goHome(): void
    {
        header("Location: " . $this->getHost(), true, 301);
        CloudStore::$app->exit();
    }
}
