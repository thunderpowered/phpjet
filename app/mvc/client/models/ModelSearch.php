<?php

namespace CloudStore\App\MVC\Client\Models;

use CloudStore\App\Engine\Components\ProductManager;
use CloudStore\App\Engine\Components\S;
use CloudStore\App\Engine\Core\Model;
use CloudStore\App\Engine\Core\System;
use CloudStore\App\Engine\Config\Config;
use CloudStore\App\Engine\Components\Utils;

/**
 *
 */
class ModelSearch extends Model
{

    public $title;
    public $brand;

    public function getPagination()
    {
        $main = "/" . Router::getRoute()[1] . "/?q=" . \CloudStore\App\Engine\Components\Request::get('q') . '&';
        return Utils::getPagination($main);
    }

    public function getProducts($array, $str, $num = 20)
    {

        $this->title = null;
        $this->brand = null;

        $slike = "%" . $str . "%";

        //First step
        $sql = "SELECT * FROM products WHERE (title LIKE ? OR brand LIKE ? OR products_sku LIKE ? OR id = ?) AND avail='1' AND price <> '0.00' AND store = ? ";
        $result = ProductManager::loadExec($sql, [$slike, $slike, $slike, (int)$str, Config::$config["site_id"]], $num);

        if ($result) {

            return $result;
        }

        //Second step
        $place = [];
        $this->title = "";
        $this->brand = "";

        foreach ($array as $key => $value) {
            if (isset($array[$key - 1])) {
                $this->title .= " AND ";
            }
            $this->title .= "title LIKE ?";
            $place[] = "%$value%";
        }

        foreach ($array as $key => $value) {
            if (isset($array[$key - 1])) {
                $this->brand .= " AND ";
            }
            $this->brand .= "(title LIKE ? OR brand LIKE ?)";
            $place[] = "%$value%";
            $place[] = "%$value%";
        }

        $place[] = \CloudStore\App\Engine\Config\Config::$config["site_id"];

        $sql = "SELECT * FROM products WHERE ( {$this->title} OR {$this->brand} ) AND avail='1' AND price <> '0.00' AND title <> '' AND store = ? ";
        $result = ProductManager::loadExec($sql, $place, $num);

        if ($result) {
            return $result;
        }

        //Last step
        $this->title = null;

        $place = [];
        $string = null;

        foreach ($array as $key => $value) {
            if (isset($array[$key - 1])) {
                $string .= " OR ";
            }
            $string .= "(title LIKE ? AND avail='1' AND price <> 0.00 AND title <> '') OR (brand LIKE ? AND avail='1' AND price <> 0.00 AND title <> '')";
            $place[] = "%$value%";
            $place[] = "%$value%";
        }

        $place[] = \CloudStore\App\Engine\Config\Config::$config["site_id"];

        $sql = "SELECT * FROM products WHERE ( $string ) AND avail='1' AND price <> '0.00' AND store = ? ";

        $result = ProductManager::loadExec($sql, $place, $num);

        if (!$result) {
            //return Route::errorPage404();
        }
        return $result;
    }

    public function getCategories($array, $query)
    {

        if (empty($query)) {

            return false;
        }

        // First step
        $categories = CloudStore::$app->store->execGet("SELECT * FROM category WHERE store = ? AND (name LIKE ? OR category_description LIKE ? OR category_id = ?)", [\CloudStore\App\Engine\Config\Config::$config["site_id"], "%" . $query . "%", "%" . $query . "%", $query]);

        if ($categories) {
            return $categories;
        }

        // Second step
        $place = [];
        $this->title = "";
        $this->brand = "";

        foreach ($array as $key => $value) {
            if (isset($array[$key - 1])) {
                $this->title .= " AND ";
            }
            $this->title .= "name LIKE ?";
            $place[] = "%$value%";
        }

        foreach ($array as $key => $value) {
            if (isset($array[$key - 1])) {
                $this->brand .= " AND ";
            }
            $this->brand .= "(name LIKE ? OR category_description LIKE ?)";
            $place[] = "%$value%";
            $place[] = "%$value%";
        }

        $place[] = \CloudStore\App\Engine\Config\Config::$config["site_id"];

        $sql = "SELECT * FROM category WHERE ( {$this->title} OR {$this->brand} ) AND store = ? ";

        $categories = CloudStore::$app->store->execGet($sql, $place, 9999);

        if ($categories) {
            return $categories;
        }
    }

    public function getPages($array, $query)
    {

        if (empty($query)) {

            return false;
        }

        // First step
        $pages = CloudStore::$app->store->execGet("SELECT * FROM pages WHERE store = ? AND ( pages_title LIKE ? OR pages_body LIKE ? )", [\CloudStore\App\Engine\Config\Config::$config["site_id"], "%" . $query . "%", "%" . $query . "%"]);

        if ($pages) {
            return $pages;
        }

        // Second step
        $place = [];
        $this->title = "";
        $this->brand = "";

        foreach ($array as $key => $value) {
            if (isset($array[$key - 1])) {
                $this->title .= " AND ";
            }
            $this->title .= "pages_title LIKE ?";
            $place[] = "%$value%";
        }

        foreach ($array as $key => $value) {
            if (isset($array[$key - 1])) {
                $this->brand .= " AND ";
            }
            $this->brand .= "(pages_title LIKE ? OR pages_body LIKE ?)";
            $place[] = "%$value%";
            $place[] = "%$value%";
        }

        $place[] = \CloudStore\App\Engine\Config\Config::$config["site_id"];

        $sql = "SELECT * FROM pages WHERE ( {$this->title} OR {$this->brand} ) AND store = ? ";

        $pages = CloudStore::$app->store->execGet($sql, $place, 9999);

        if ($pages) {
            return $pages;
        }
    }
}
