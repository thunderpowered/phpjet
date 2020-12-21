<?php

namespace CloudStore\App\Engine\Controllers;

use CloudStore\App\Engine\Components\Request;
use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\Engine\Core\Router;
use CloudStore\App\Engine\Core\System;
use CloudStore\App\Engine\Components\Utils;

class ControllerFilter extends Controller
{

    public function actionBasic()
    {

        if (!\CloudStore\App\Engine\Components\Request::get()) {
            return Utils::strongRedirect('catalog', 'all');
        }

        $csrf = Request::get('csrf');

        if (!Utils::validateToken($csrf)) {

            //return Router::hoHome();
        }

        //Getting products
        $products = $this->model->Filter();

        // Count
        $string = Router::getRoute()[2] ?? null;
        //Getting Category Name

        $category = $this->model->getCategory(Request::get('category_name'));
        if(!$category) {

            Router::errorPage404();
        }

        $this->title = !empty($category) ? $category['name'] : "Каталог товаров";

        return $this->view->render($this->view->getTemplateName(), [
            'filter_products' => $products,
            'category' => $category,
            'category_handle' => $category['category_handle']
        ]);
    }

    public function getPagination()
    {
        if (Request::get()) {

            return $this->model->getPagination();
        }

        return false;
    }
}
