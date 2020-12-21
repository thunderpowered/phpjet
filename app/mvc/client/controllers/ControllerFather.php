<?php

namespace CloudStore\App\Engine\Controllers;

use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\Engine\Core\Router;

class ControllerFather extends Controller
{

    public function type()
    {

        return "act";
    }

    public function actionedit()
    {

        if (empty(\CloudStore\App\Engine\Config\Config::$dev["FatherKey"])) {

            Router::ErrorPage404();
        }

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {

            die("Forbidden");
        }

        $post = \CloudStore\App\Engine\Components\Request::post();

        if (isset($post["domain"])) {

            $hash = hash("sha256", $post["id"] . $post["users_id"] . $post["name"] . $post["email"] . $post["smtp"] . $post["smtp_password"] . $post["domain"] . \CloudStore\App\Engine\Config\Config::$dev["FatherKey"]);
        } else {

            $hash = hash("sha256", $post["id"] . $post["users_id"] . $post["name"] . $post["email"] . $post["smtp"] . $post["smtp_password"] . \CloudStore\App\Engine\Config\Config::$dev["FatherKey"]);
        }

        if ($post["token"] !== $hash) {

            die("Forbidden");
        }


        return $this->model->editStore($post);
    }

    public function actioncreate()
    {

        if (empty(\CloudStore\App\Engine\Config\Config::$dev["FatherKey"])) {

            Router::ErrorPage404();
        }

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {

            die("Forbidden");
        }

        $post = \CloudStore\App\Engine\Components\Request::post();

        $hash = hash("sha256", $post["id"] . $post["store"] . $post["name"] . $post["email"] . $post["domain"] . $post["date"] . $post["days"] . \CloudStore\App\Engine\Config\Config::$dev["FatherKey"]);

        if ($post["token"] !== $hash) {

            die("Forbidden");
        }


        return $this->model->createStore($post);
    }

    public function actiondelete()
    {

        if (empty(\CloudStore\App\Engine\Config\Config::$dev["FatherKey"])) {

            Router::ErrorPage404();
        }

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {

            Router::ErrorPage404();
        }

        $post = \CloudStore\App\Engine\Components\Request::post();

        $hash = hash("sha256", $post["id"] . $post["users_id"] . $post["email"] . \CloudStore\App\Engine\Config\Config::$dev["FatherKey"]);

        if ($hash !== $post["token"]) {

            die("Forbidden");
        }

        return $this->model->deleteStore($post);
    }

    public function actionsetdate()
    {

        if (empty(\CloudStore\App\Engine\Config\Config::$dev["FatherKey"])) {

            Router::ErrorPage404();
        }

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {

            die("Forbidden");
        }

        $post = \CloudStore\App\Engine\Components\Request::post();

        $hash = hash("sha256", $post["id"] . $post["days"] . $post["date"] . \CloudStore\App\Engine\Config\Config::$dev["FatherKey"]);

        if ($post["token"] !== $hash) {

            die("Forbidden");
        }

        return $this->model->setPayment($post);
    }
}
