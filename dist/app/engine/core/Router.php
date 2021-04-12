<?php

namespace Jet\App\Engine\Core;

use Exception;
use Jet\App\Engine\Config;
use Jet\App\Engine\Exceptions\CoreException;
use Jet\App\Engine\Exceptions\WrongDataException;
use Jet\App\Engine\Interfaces\MessageBox;
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
     * @var string
     */
    private $controllerName;
    /**
     * @var string
     */
    private $controllerUrl;
    /**
     * @var array
     */
    private $actionList;
    /**
     * @var array
     */
    private $default = [
        'controller' => 'main',
        'model' => 'main'
    ];
    /**
     * @var array
     * @deprecated
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
     * @var string
     */
    private $urlConfigFilename = 'urls.php';

    /**
     * 1.
     * @return string
     */
    public function start(): string
    {
        $this->subdomain = $this->getSubdomain();
        $this->MVCSector =  Config\Config::$config[$this->subdomain] ? Config\Config::$urlRules[$this->subdomain] : Config\Config::$urlRules['']; // mostly temp
        define('MVC_SECTOR', $this->MVCSector);
        define('MVC_PATH', PHPJet::$app->system->getMVCPath($this->MVCSector));
        define('NAMESPACE_MVC_ROOT', NAMESPACE_ROOT . "\App\MVC\\" . $this->MVCSector . "\\");

        // page builder is disabled at the moment
        if (false && Config\Config::$pageBuilder[MVC_SECTOR]['active']) {
            $page = PHPJet::$app->pageBuilder->getPageData($this->getURL(false));
            if ($page) {
                return $this->proceedRouterScheme('pageBuilder');
            }
        }
        return $this->proceedRouterScheme('default');
    }

    /**
     * 2.
     * @param string $scheme
     * @return string
     * @deprecated
     */
    private function proceedRouterScheme(string $scheme): string
    {
        $url = $this->getURL(false);
        switch ($scheme) {
            case 'pageBuilder':
                $this->controllerName = 'page';
                break;
            case 'default':
                /**
                 * @var string $controllerName
                 * @var array $data
                 */
                extract($this->compileControllerName($url));
                $this->controllerName = $controllerName;
                if ($data) {
                    $this->controllerUrl = $data['url'];
                }
                if (!$this->controllerName || !PHPJet::$app->system->isControllerActive($this->controllerName, $this->MVCSector)) {
                    return $this->errorPage404();
                }
                break;
            default:
                return $this->errorPage404();
        }
        $this->controller = $this->getControllerObject($this->controllerName);
        if (
            !$this->controller
            || !$this->doesControllerSupportRequestMethod($this->controller)
            || !$this->checkURLToken($this->controller)
        ) {
            return $this->errorPage404();
        }
        return $this->startAction($this->controller, $url, $this->controllerUrl, $this->controllerName);
    }

    /**
     * 3.
     * @param Controller $controller controller object
     * @param string $url current url
     * @param string $controllerUrl url from urls.php that matches current url
     * @param string $controllerName name of controller, btw it could be gotten using getName function, but it is also OK
     * @return string
     */
    private function startAction(Controller $controller, string $url, string $controllerUrl, string $controllerName): string
    {
        if ($controllerUrl) {
            $controllerUrl = str_replace($controllerUrl, '', $url);
        }
        /**
         * @var string $actionName
         * @var array $data
         */
        extract($this->getActionName(false, $controllerUrl, $controllerName));
        if (!$actionName) {
            return $this->errorPage404();
        }
        $actionName = $this->actionPrefix . $actionName;
        if (method_exists($controller, $actionName) && is_callable([$controller, $actionName])) {
            try {
                $data['params'] = $this->proceedQueryParams($data['params']);
            } catch (Exception $exception) {
                return $this->serverErrorPage($exception);
            }
            $result = call_user_func_array([$controller, $actionName], $data['params']);
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
     * @param bool $forceRedirect
     * @param string $information
     * @return string
     * @deprecated
     */
    public function errorPage404(bool $forceRedirect = false, string $information = 'Not Found'): string
    {
        return $this->errorPage('404', 'Not Found', '404', $forceRedirect, $information);
    }

    /**
     * @param bool $forceRedirect
     * @param string $information
     * @deprecated
     * @return string
     */
    public function errorPage500(bool $forceRedirect = false, string $information = 'Internal Server Error'): string
    {
        return $this->errorPage('500', 'Internal Server Error', 'error', $forceRedirect, $information);
    }

    /**
     * @param Exception $exception
     * @param bool $forceRedirect
     * @return string
     */
    public function serverErrorPage(CoreException $exception, bool $forceRedirect = false): string
    {
        return $this->errorPage($exception->getCode(), $exception->getMessage(), 'error', $forceRedirect, $exception->getNotes());
    }

    /**
     * @param string $code
     * @param string $message
     * @param string $layout
     * @param bool $forceRedirect
     * @param string $information
     * @return string
     */
    public function errorPage(string $code = '500', string $message = 'Internal Server Error', string $layout = 'error', bool $forceRedirect = false, string $information = ''): string
    {
        if (!$message) {
            $message = $this->httpErrorCodes[$code] ?? null;
        }

        $view = $this->getViewObject(new Controller());
        $view->setLayout($layout);
        $view->buffer->destroyBuffer();
        header("HTTP/1.1 {$code} {$message}");
        $result = $view->render("default", [], false, '', new MessageBox(1, $information));
        if ($forceRedirect) {
            // it's not a redirect technically, it just stops any further actions
            echo $result->response;
            PHPJet::$app->exit();
        }
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
     * @param string $controllerName
     * @return Controller
     */
    private function getControllerObject(string $controllerName): Controller
    {
        /**
         * @var Controller $controller
         */
        $controller = null;

        if ($controllerName) {
            $controllerName = 'Controller' . $controllerName;
        }
        // Namespace sector
        $MVCRoot = $this->MVCSector ? ucfirst($this->MVCSector) : ucfirst(Config\Config::$urlRules['']);
        // Create full controller name with namespace
        $Class = "\Jet\App\MVC\\$MVCRoot\Controllers\\" . $controllerName;
        if (class_exists($Class)) {
            $controller = new $Class($controllerName, true);
        } else {
            PHPJet::$app->exit("Class '{$Class}' Not Found");
        }

        // Create and set view
        $view = $this->getViewObject($controller);
        $view->loadWidgets();
        $controller->setView($view);

        return $controller;
    }

    /**
     * @param bool $lowerCase
     * @return string
     */
    public function getControllerName(bool $lowerCase = false): string
    {
        return $lowerCase ? strtolower($this->controllerName) : $this->controllerName;
    }

    /**
     * @param string $controllerUrl
     * @param bool $lowerCase
     * @return array
     */
    public function compileControllerName(string $controllerUrl, bool $lowerCase = false): array
    {
        $controllerName = [
            'controllerName' => '',
            'data' => []
        ];

        try {
            $urls = $this->parseURLConfig(MVC_PATH);
        } catch (Exception $e) {
            return $controllerName;
        }

        $url = $this->findMatchesInURL($controllerUrl, $urls);
        if ($url) {
            $controllerName['controllerName'] = $lowerCase ? strtolower($url['key']) : $url['key'];
            $controllerName['data'] = $url['data'];
        }

        return $controllerName;
    }

    /**
     * @param bool $lowerCase
     * @param string $actionUrl
     * @param string $controllerName
     * @return array
     */
    public function getActionName(bool $lowerCase, string $actionUrl, string $controllerName): array
    {
        $actionName = [
            'actionName' => '',
            'data' => []
        ];
        try {
            $urls = $this->parseURLConfig(MVC_PATH . 'controllers/'); // todo maybe define another constant?
        } catch (Exception $e) {
            return $actionName;
        }
        if (!isset($urls[$controllerName]) || !isset($urls[$controllerName]['actions'])) {
            return $actionName;
        }
        $url = $this->findMatchesInURL($actionUrl, $urls[$controllerName]['actions'] ?? [], true);
        if ($url) {
            $actionName['actionName'] = $lowerCase ? strtolower($url['key']) : $url['key'];
            $actionName['data'] = $url['data'];
        }
        return $actionName;
    }

    /**
     * @param string $controllerName
     * @return bool
     */
    public function setControllerName(string $controllerName, bool $loserCase = false): bool
    {
        if (!PHPJet::$app->system->isControllerActive($controllerName, $this->MVCSector, true)) {
            return false;
        }

        $this->controllerName = $controllerName;
        return true;
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
     * @param string $path
     * @return array
     * @throws Exception
     */
    private function parseURLConfig(string $path): array
    {
        $fileName = $path . $this->urlConfigFilename;
        if (!file_exists($fileName)) {
            throw new Exception($this->urlConfigFilename . ' does not exist in ' . $path);
        }
        // temporary solution, need better way to do it
        require_once $fileName;
        if (!isset($urls) || !method_exists($urls, 'getUrls')) {
            throw new Exception($this->urlConfigFilename . ' is set incorrectly');
        }
        return $urls->getUrls();
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
     */
    private function getSubdomain(): string
    {
        $domain = $this->getDomain();
        $domain = explode('.', $domain);
        return $domain[0] ?? '';
    }

    /**
     * @param array $params
     * @param bool $validateData
     * @return array
     * @throws Exception
     */
    private function proceedQueryParams(array $params, bool $validateData = true): array
    {
        $method = PHPJet::$app->system->request->getRequestMethod();
        // quick check if method exists in config
        if (!isset($params[$method])) {
            throw new WrongDataException("method '{$method}' is not supported by this action");
        }
        $result = [$method];
        foreach ($params as $paramMethod => &$paramData) {
            foreach ($paramData as $key => $type) {
                $funcName = "get{$paramMethod}";
                if (!method_exists(PHPJet::$app->system->request, $funcName)) {
                    throw new WrongDataException("unexpected method");
                }
                $value = PHPJet::$app->system->request->{$funcName}($key);
                if ($validateData && $method === $paramMethod) {
                    // 1. check if no data at all
                    if (!$value) {
                        throw new WrongDataException("parameter '{$key}' cannot be empty");
                    }
                    // 2. todo validate data
                    /*
                    $validated = some_magic_function($type, $value);
                    if (!$validated) {
                        throw new Exception("value of parameter '{$key}' does not match the requirements");
                    }
                    */
                }
                $paramData[$key] = $value;
            }
            $result[$paramMethod] = $paramData;
            unset ($paramData);
        }
        return $result;
    }

    /**
     * @param string $string
     * @param array $urls
     * @param bool $exact
     * @return array
     */
    private function findMatchesInURL(string $string, array $urls, bool $exact = false): array
    {
        // todo this algo is not the best possible (obviously), redo it someday
        // 0. prepare the string (remove get-params and add first slash
        $string = explode('?', $string);
        $string = (string)reset($string);
        if (substr($string, 0, 1) !== "/") {
            $string  = "/{$string}";
        }

        // 1. match multiple patterns against one string
        $matches = [];
        foreach ($urls as $key => $data) {
            $pattern = str_replace("/", "\/", $data['url']);
            $pattern = $exact ? "/^{$pattern}$/" : "/^{$pattern}/";
            $current = [];
            if (preg_match($pattern, $string, $current)) {
                $matches[ strlen($current[0]) ] = [
                    'key' => $key,
                    'data' => $data,
                ];
            }
        }

        // 2. return empty array if nothing found
        if (!count($matches)) {
            return [];
        }

        // 3. sort it and return the longest string that match the pattern
        ksort($matches);
        return end($matches);
    }
}
