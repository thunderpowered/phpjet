<?php

namespace CloudStore\App\Engine\Controllers;

use CloudStore\App\Engine\Components\S;
use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\Engine\Core\System;

class ControllerSection extends Controller
{

    public function actionBasic()
    {
        //Getting parameter
        $category = Router::getAction();

        //Getting products
        $products = $this->model->getProducts($category);

        //Getting Category_name
        $category_name = $this->model->getCategoryName($category);

        //Getting category_id
        $category_id = CloudStore::$app->store->loadOne("category", ["category_handle" => $category]);

        $category_id = $category_id["category_id"] ?? null;

        $this->title = $category_name;

        return $this->view->render("view_catalog", [
            'cat_products' => $products,
            'category_name' => $category_name,
            'category_id' => $category_id
        ]);
    }

    public function SEO()
    {
        return [
            'name' => [
                'description' => 'Каталог товаров: ' . $this->title
            ]
        ];
    }

    public function getPagination()
    {
        return $this->model->getPagination();
    }
}
