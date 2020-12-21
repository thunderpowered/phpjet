<?php

namespace CloudStore\App\MVC\Client\Models;

use CloudStore\App\Engine\Core\Model;
use CloudStore\CloudStore;

/**
 * Class ModelFilter
 * @package CloudStore\App\MVC\Client\Models
 * @deprecated until fixed
 */
class ModelFilter extends Model
{

    public $final;
    public $attributes;
    public $start = 0;
    public $param = [];

    public function getPagination()
    {
        $main = "/" . Router::getRoute()[1] . "/" . Router::getAction(false, false) . '&';
        return Pagination::getPagination($main);
    }

    public function filter()
    {

        return Filter::getProducts();
    }

    public function getCategory($url)
    {
        $id = intval($url);
        $category = CloudStore::$app->store->loadOne("category", ["category_id" => $id], false);

        if ($category) {

            return $category;
        }

        $handle = Utils::makeHandle($url);
        $category = CloudStore::$app->store->loadOne("category", ["category_handle" => $handle], false);

        if ($category) {

            return $category;
        }

        return [];
    }
}
