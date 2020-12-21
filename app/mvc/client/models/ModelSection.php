<?php

namespace CloudStore\App\MVC\Client\Models;

use CloudStore\App\Engine\Components\ProductManager;
use CloudStore\App\Engine\Core\Model;
use CloudStore\App\Engine\Core\Router;
use CloudStore\App\Engine\Core\System;
use CloudStore\App\Engine\Components\Utils;

/**
 *
 */
class ModelSection extends Model
{

    public function getPagination()
    {
        $main = "/" . Router::getRoute()[1] . "/" . Router::getAction() . '?';
        return Utils::getPagination($main);
    }

    public function getProducts($category)
    {
        $sql = "SELECT * FROM products WHERE avail='1' AND price <> 0.00 AND category_id IN (SELECT category_id FROM category WHERE section=:section)";
        $result = ProductManager::loadExec($sql, [":section" => $category]);
        if (!$result) {
            Router::errorPage404();
        }
        return $result;
    }

    public function getCategoryName($category)
    {
        switch ($category) {
            case 'children':
                return 'Детям';
                break;
            case 'dentist':
                return 'Врачам';
                break;
            default:
                return 'Каталог товаров';
                break;
        }
    }
}
