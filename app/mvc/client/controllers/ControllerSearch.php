<?php

namespace CloudStore\App\Engine\Controllers;

use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\Engine\Core\System;

class ControllerSearch extends Controller
{

    public static $count;

    public function actionBasic()
    {
        // Set noindex
        $this->noIndex = true;

        // Getting parameter
        $query = trim(mb_strtolower(\CloudStore\App\Engine\Components\Request::get('q')));

        if (empty($query)) {

            return $this->view->render($this->view->getTemplateName(), [
                'search_products' => false,
                'search_count' => 0,
                'categories' => false,
                'pages' => false
            ]);
        }

        $array = explode(' ', $query);

        // Getting products
        $products = $this->model->getProducts($array, $query);

        // Get categories
        $categories = $this->model->getCategories($array, $query);

        // Get pages
        $pages = $this->model->getPages($array, $query);

        //Entering into session
        \CloudStore\App\Engine\Components\Request::setSession('query', $query);

        return $this->view->render($this->view->getTemplateName(), [
            'search_products' => $products,
            'categories' => $categories,
            'pages' => $pages
        ]);
    }

    public function getPagination()
    {
        return $this->model->getPagination();
    }
}
