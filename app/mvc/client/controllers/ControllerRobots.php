<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 2018-06-21
 * Time: 7:50
 */

namespace CloudStore\App\Engine\Controllers;

use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\Engine\Core\Router;
use CloudStore\App\Engine\Core\System;

class ControllerRobots extends Controller
{
    public function actionBasic()
    {

        $robots = Router::getLastRoutePart();

        if (!strpos($robots, ".txt")) {

            Router::errorPage404();
        }

        $this->model->robots();
    }
}