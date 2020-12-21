<?php

namespace CloudStore\App\Engine\Controllers;

use CloudStore\App\Engine\Components\S;
use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\Engine\Core\Router;
use CloudStore\App\Engine\Core\System;
use CloudStore\App\Engine\Components\Utils;

class ControllerPages extends Controller
{

    public $errors;

    public static function getData()
    {
        $handle = Router::getAction();

        $sql = "SELECT * FROM pages WHERE pages_handle=?";

        $content = CloudStore::$app->store->loadOne("pages", ["pages_handle" => $handle], false);

        if (!$content < 1 OR !$handle) {
            Router::errorPage404();
        }
        return $content;
    }

    public static function getForm()
    {
        $handle = Router::getAction();


        // i'll get rule in config later
        if ($handle === 'contact') {
            return true;
        }

        return false;
    }

    public function actionBasic()
    {
        if (\CloudStore\App\Engine\Components\Request::post('contact')) {
            $csrf = \CloudStore\App\Engine\Components\Request::post('csrf');
            if (!Utils::validateToken($csrf)) {
                return false;
            }

            $this->errors = $this->model->validate();
            if (!$this->errors) {

                if (!$this->model->feedback()) {
                    return false;
                }

                \CloudStore\App\Engine\Components\Request::setSession('contact_message', 'success');
            } else {
                \CloudStore\App\Engine\Components\Request::setSession('contact_message', 'error');
            }
        }

        $handle = Router::getAction();

        $content = CloudStore::$app->store->loadOne("pages", ["pages_handle" => $handle], false);

        if (!$content) {
            Router::errorPage404();
        }

        $this->title = $content['pages_title'];

        return $this->view->render($this->view->getTemplateName(), [
            'content' => $content
        ]);
    }

    public function SEO()
    {
        return [
            'name' => [
                'description' => 'Информация для покупателей: ' . $this->title
            ]
        ];
    }
}
