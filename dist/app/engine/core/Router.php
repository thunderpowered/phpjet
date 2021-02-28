<?php

namespace Jet\App\Engine\Core;

use Jet\App\Engine\Config;
use Jet\PHPJet;

/**
 * Class Router
 * @package Jet\App\Engine\Core
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
    private $actionPrefix = 'action';
    /**
     * @var string
     */
    private $actionBasic = "basic";
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
    private $httpCodes = [
        '200' => 'OK',
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
     * @var string
     */
    private $subdomain;

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
        $this->MVCSector = Config\Config::$config['admin'] ? Config\Config::$urlRules['admin'] : Config\Config::$urlRules['']; // mostly temp
        define('MVC_SECTOR', $this->MVCSector);
        define('MVC_PATH', PHPJet::$app->system->getMVCPath($this->MVCSector));
        define('NAMESPACE_MVC_ROOT', NAMESPACE_ROOT . "\App\MVC\\" . $this->MVCSector . "\\");

        // page builder is disabled at the moment
        if (Config\Config::$pageBuilder[MVC_SECTOR]['active']) {
            $page = PHPJet::$app->pageBuilder->getPageData($this->getURL(false)); // or get exception
            if ($page) {
                return $this->proceedRouterScheme('pageBuilder'); // todo pass the data to it
            }
        }
        return $this->proceedRouterScheme('default');
    }

    /**
     * @param string $scheme
     * @return string
     */
    private function proceedRouterScheme(string $scheme = 'default'): string
    {
        if ($scheme === 'pageBuilder') {
            $this->setControllerName('page');
        } else {
            $controllerName = $this->getControllerName(true);
            // check if this controller in the list of controllers
            // i personally think it'd be better to check the property of controller, not the list
            if (!PHPJet::$app->system->isControllerActive($controllerName, $this->MVCSector)) {
                return $this->errorPage404();
            }
        }

        $this->controller = $this->getControllerObject();

        if (
               !$this->controller
            || !$this->doesControllerSupportRequestMethod($this->controller)
            || !$this->checkURLToken($this->controller)
        ) {
            return $this->errorPage404();
        }

        return $this->startAction($this->controller);
    }

    /**
     * @param $controller
     * @return string
     */
    private function startAction(Controller $controller): string
    {
        $action = $this->parseURL($this->getRoute());
        var_dump($action);
        exit();
        if ($action && method_exists($controller, $action['actionName']) && is_callable([$controller, $action['actionName']])) {
            $result = call_user_func_array([$controller, $action['actionName']], $action['parameters']);
            if (!$result->SPA && PHPJet::$app->system->request->getRequestMethod() !== 'GET') {
                $this->refresh();
            }

            return $result->response;
        }

        return $this->errorPage404();
    }

    /**
     * @param string $content
     */
    public function immediateResponse(string $content = '')
    {
        PHPJet::$app->system->buffer->destroyBuffer();
        echo $content;
        PHPJet::$app->exit();
    }

    /**
     * @return string
     * @deprecated
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
            echo $result->response;
            PHPJet::$app->exit();
        }
        // todo if SPA return JSON
        return $result->response;
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
        $delimiterPosition = PHPJet::$app->tool->utils->strpos($part, $this->delimiter);
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

        $requestURI = PHPJet::$app->system->request->getSERVER('REQUEST_URI');
        $this->route = explode('/', $requestURI);

        if ($removeSpecialChars) {
            $this->route = PHPJet::$app->tool->utils->removeSpecialChars($this->route);
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

        $delimiterPosition = PHPJet::$app->tool->utils->strpos($part, $this->delimiter);
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
        $delimiterPosition = PHPJet::$app->tool->utils->strpos($string, $this->delimiter);
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
        return PHPJet::$app->system->request->getSERVER('HTTP_HOST');
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
        $https = PHPJet::$app->system->request->getSERVER('HTTPS');
        $scheme = "http" . ($https ? 's' : '') . "://";
        return $scheme . $this->getDomain();
    }

    /**
     * @param bool $removeSpecialChars
     * @return string
     */
    public static function getRequestURI(bool $removeSpecialChars = true): string
    {
        $request = PHPJet::$app->system->request->getSERVER('REQUEST_URI');

        if ($removeSpecialChars) {
            return PHPJet::$app->tool->utils->removeSpecialChars($request);
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
        return PHPJet::$app->system->request->getSERVER('REQUEST_URI');
    }

    /**
     * @param bool $includeHost
     * @return string
     */
    public function getURL(bool $includeHost = true): string
    {
        $url = PHPJet::$app->system->request->getSERVER('REQUEST_URI');
        if ($includeHost) {
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
        $MVCRoot = $this->MVCSector ? ucfirst($this->MVCSector) : ucfirst(Config\Config::$urlRules['']);
        // Create full controller name with namespace
        $Class = "\Jet\App\MVC\\$MVCRoot\Controllers\\" . $name;
        // Create object
        if (class_exists($Class)) {
            $controller = new $Class($name, true);
        } else {
            // If there is no such class
            PHPJet::$app->exit("Class '{$Class}' Not Found");
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

        if ($lowerCase) {
            $this->controllerName = strtolower($this->controllerName);
        } else {
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
        if (!PHPJet::$app->system->isControllerActive($controllerName, $this->MVCSector, true)) {
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

        $Class = '\Jet\App\Engine\Models\\' . $name;
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
     * @param bool $includeFullName
     * @param bool $cutToDelimiter
     * @return bool|mixed|string
     */
    public function getAction(bool $includeFullName = false, bool $cutToDelimiter = true): string
    {
        $action = $this->getRoutePart($this->defaultActionRoutePart, true, $cutToDelimiter);
        if ($action && $includeFullName) {
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

    public function refresh(): void
    {
        header("Location: " . $this->getURL(), true, 301);
        PHPJet::$app->exit();
    }

    /**
     * @param string $url
     * @param int $code
     */
    public function redirect(string $url, $code = 301): void
    {
        header("Location: " . $url, true, $code);
        PHPJet::$app->exit();
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
        PHPJet::$app->exit();
    }

    /**
     * @param Controller $controller
     * @return bool
     * @deprecated
     */
    private function doesControllerSupportRequestMethod(Controller $controller): bool
    {
        $supportedMethods = $controller->getSupportedQueryMethods();
        $actualMethod = PHPJet::$app->system->request->getRequestMethod();
        if (!in_array($actualMethod, $supportedMethods)) {
//            return false;
        }

        // and also check the special case
        // by PHPJet agreement all POST-queries must contain valid csrf-token
        if (($actualMethod === 'POST' || $actualMethod === 'PUT') && !PHPJet::$app->system->request->checkCSRFToken()) {
            return false;
        }

        return true;
    }

    /**
     * @param Controller $controller
     * @return bool
     * @deprecated
     */
    private function checkURLToken(Controller $controller): bool
    {
        $isURLTokenRequired = $controller->isTokenRequired();
        if (!$isURLTokenRequired) {
            return true;
        }

        $URLTokenURLKey = $controller->getURLTokenURLKey();
        $URlTokenSessionKey = $controller->getURLTokenSessionKey();
        $tokenInURL = PHPJet::$app->system->request->getGET($URLTokenURLKey);
        $tokenInSession = PHPJet::$app->system->request->getSESSION($URlTokenSessionKey);
        if (!$tokenInURL || !$tokenInURL || $tokenInURL !== $tokenInSession) {
            return false;
        }

        return true;
    }

    /**
     * @param array $route
     * @param bool $removeSpecialChars
     * @return array
     */
    private function parseURL(array $route, bool $removeSpecialChars = true): array
    {
        $actionName = $this->actionPrefix;
        $parameters = [];
        if (empty($route[$this->defaultActionRoutePart + 1])) {
            $actionName .= ucfirst($this->actionBasic);
        }
        for ($i = $this->defaultActionRoutePart, $l = count($route) + 1; $i < $l && isset($route[$i]); $i++) {
            if ($i % 2 === 0) {
                // ID
                $parameterName = ($route[$i - 1] ? strtoupper($route[$i - 1]) . '_' : '') . 'ID';
                $parameters[$parameterName] = $route[$i] ?? null;
            } else {
                // ACTION
                $actionName .= ucfirst($route[$i]);
            }
        }
        // include method
        $actionName .= PHPJet::$app->system->request->getRequestMethod();
        return [
            'actionName' => $actionName,
            'parameters' => $parameters
        ];
    }

    /**
     * @return string
     * @deprecated
     */
    private function getSubdomain(): string
    {
        $domain = $this->getHost();
        $domain = explode('.', $domain);
        return isset($domain[0]) ? $domain[0] : '';
    }
}
