<?php

class Connect
{

    public $result;

    public static function connect_router()
    {
        $routes = explode('/', $_SERVER['REQUEST_URI']);

        if (!empty($routes[2])) {
            $handler_class = ucfirst($routes[2]);
            $handler_name = '../engine/connect/' . $routes[2] . '.php';
        }

        if (!file_exists($handler_name)) {
            return false;
        }

        require($handler_name);

        $handler = new $handler_class;

        $handler->start();

        exit();
    }
}

//connect::connect_router();
