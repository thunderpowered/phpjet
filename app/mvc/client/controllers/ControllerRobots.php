<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 2018-06-21
 * Time: 7:50
 */

namespace Jet\App\Engine\Controllers;

use Jet\App\Engine\Core\Controller;
use Jet\App\Engine\Core\Router;

/**
 * Class ControllerRobots
 * @package Jet\App\Engine\Controllers
 * @deprecated
 */
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