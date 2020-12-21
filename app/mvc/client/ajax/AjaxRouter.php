<?php

namespace CloudStore\App\Engine\Ajax;

use CloudStore\App\Engine\CloudStore;
use CloudStore\App\Engine\Core\Router;
use CloudStore\App\Engine\Core\System;

class AjaxRouter
{
    /**
     * @var array
     */
    private $route;

    /**
     * @var string
     */
    private $handlerClass;

    /**
     * @var string
     */
    private $handlerName;

    /**
     * AjaxRouter constructor.
     */
    public function __construct()
    {
        $this->route = CloudStore::$app->router->getRoute();

        if (!empty($this->route[2])) {
            $this->handlerClass = CloudStore::$app->router->getAction();
            $this->handlerClass = strtolower($this->handlerClass);
            $this->handlerClass = ucfirst($this->handlerClass);
            $this->handlerClass = 'Ajax' . $this->handlerClass;
            $this->handlerName = $this->handlerClass . '.php';

            require_once ENGINE . 'ajax/handlers/' . $this->handlerName;
        } else {
            CloudStore::$app->router->errorPage500();
        }
    }

    public static function start()
    {
        $routes = Router::getRoute();

        if (!empty($routes[2])) {
            $handler_class = 'Ajax' . ucfirst(strtolower(Router::getAction()));
            $handler_name = $handler_class . '.php';
        }


        if (!file_exists(ENGINE . 'ajax/handlers/' . $handler_name)) {

            return false;
        }

        if (empty($routes[3])) {
            return false;
        }

        $action = Router::getOption();

        $handler_class = "CloudStore\App\Engine\Ajax\Handlers\\" . $handler_class;

        if (!class_exists($handler_class)) {
            return false;
        }

        $handler = new $handler_class;

        if (!method_exists($handler, $action)) {
            return false;
        }

        return $handler->$action();
    }
}
