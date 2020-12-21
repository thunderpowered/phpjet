<?php

namespace CloudStore\App\Engine\Controllers;

use CloudStore\App\Engine\Core\Controller;

class ControllerErrorpage extends Controller
{

    public function actionBasic()
    {
//        $this->view->layout = "error";

        header("HTTP/1.1 500 Internal Server Error");

        $layout = HOME . 'templates/layout/default/error.php';
        if (file_exists($layout)) {
            require_once $layout;
        }
//        return $this->view->render("view_errorpage", [
//            'error_message' => 'Произошла ошибка на сайте. Мы уже получили сообщение об этом. Попробуйте, пожалуйста, еще раз через некоторое время.'
//        ]);
    }
}
