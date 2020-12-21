<?php

namespace CloudStore\App\Engine\Controllers;

use CloudStore\App\Engine\Core\Controller;

class Controller404 extends Controller
{

    public function actionBasic()
    {
        header("HTTP/1.1 404 Not Found");
        return $this->view->render("404");
    }
}
