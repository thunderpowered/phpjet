<?php

namespace CloudStore\App\MVC\Client\Models;

use CloudStore\App\Engine\Config\Config;
use CloudStore\App\Engine\Core\Model;
use CloudStore\App\Engine\Core\Router;
use CloudStore\App\Engine\Core\System;
use CloudStore\CloudStore;

/**
 *
 */
class ModelCatalog extends Model
{

    public $final;
    public $vari;
    public $attributes;
    public $start = 0;
    public $filters;
    public $attributes_info;

    // Temporary
    // It will be loaded from db
    // @todo get from db
    private $descriptionTemplate = "На нашем сайте вы можете найти товары из категории <b>{{CAT}}</b>";

    public function getPagination(): string
    {
        return '';
        $main = "/" . Router::getRoute()[1] . "/" . Router::getAction() . '?';
        return Utils::getPagination($main);
    }

    /**
     * @param $category
     * @return bool
     * @deprecated
     */
    public function getProducts($category)
    {

        if (!$category) {

            $products = ProductManager::load(["avail" => "!0", "price" => "!0.00"], 12);
        } else {

            //$products = ProductS::load( ["avail" => "!0", "price" => "!0.00", "category_id" => $category], 12 );

            $categories = $this->getRecursiveCategories($category);
            if (!$categories) {
                return false;
            }
            $placeholders = implode(",", array_fill(0, count($categories), "?"));
            array_push($categories, Config::$config["site_id"]);
            $products = ProductManager::loadExec("SELECT * FROM products_category pc INNER JOIN products p ON pc.id = p.id AND pc.store = p.store WHERE pc.category_id IN ($placeholders) AND pc.store = ? AND p.avail = '1' ", $categories, 12);
        }

        return $products;
    }

    private function getRecursiveCategories($category)
    {
        // Still not recursive
        $categories = CloudStore::$app->store->load("category", ["parent" => $category]);
        if ($categories) {
            foreach ($categories as $key => $_category) {
                $categories[$key] = (int)$_category["category_id"];
            }
        }
        if (!$categories) {
            $categories = [];
        }

        array_unshift($categories, $category);
        return $categories;
    }

    public function getCategoryName($category)
    {

        $array = CloudStore::$app->store->loadOne("category", ["category_handle" => $category]);

        if ($array) {
            if (array_key_exists('name', $array)) {
                $category_name = $array['name'];
            }
        } else {
            $category_name = 'Каталог товаров';
        }

        return $category_name;
    }

    /**
     * @param string $url
     * @return array
     */
    public function getCategory(string $url): array
    {
        // first part of the url is number that points to category id, so we just parse it
        $id = intval($url);
        $category = CloudStore::$app->store->loadOne("category", ["category_id" => $id], false);

        if ($category) {
            $category["category_description"] = $this->prepareDescription($category["category_description"], $category["name"]);
            return $category;
        }

        // there is a different situation when there is no category id in the URL, but in this case we also want to find products
        // just prepare proper URL and redirect
        $categoryHandler = CloudStore::$app->tool->utils->makeHandler($url);
        $category = CloudStore::$app->store->loadOne("category", ["category_handle" => $categoryHandler], false);

        if($category) {
            $url = "catalog/" . $category["category_id"] . "-" . $category["category_handle"];
            CloudStore::$app->tool->utils->redirect($url);
        }

        return [];
    }

    /**
     * @param string $description
     * @param string $category
     * @return string
     */
    private function prepareDescription(string $description, string $category): string
    {
        if ($description) {
            return $description;
        }

        return CloudStore::$app->tool->utils->tplPrepare($this->descriptionTemplate, ["CAT"], [$category]);
    }

    // @todo
    public function preparedFilters($category_id, $category_name)
    {

        return [];

        /*
          !!!
          @todo REPLACE ALL "GETTER" QUERIES WITH CloudStore::$app->store-> QUERIES
          !!!
         */


        $query = null;
        $filters = [];

        $prepared_filters = CloudStore::$app->store->load("sef_prepared_filters", ["filter_category" => $category_id]);

        if (!$prepared_filters) {
            return false;
        }

        foreach ($prepared_filters as $filter) {
            $blocks = explode(PHP_EOL, $filter['filter_value']);

            $sybmol = " - ";

            if (!empty($blocks)) {
                foreach ($blocks as $block) {

                    $attribute = strpos($block, $sybmol) ? trim(substr($block, 0, strpos($block, $sybmol))) : $block;

                    $attribute_id = CloudStore::$app->store->loadOne("sef_filters_category", ["attribute_name" => $attribute, "category_id" => $category_id]);

                    //$attribute_id = \CloudStore\App\Engine\Components\Getter::getFreeData("SELECT * FROM sef_filters_category WHERE attribute_name = ? AND category_id = ?", [$attribute, $category_id], true);

                    $value = trim(substr($block, strpos($block, $sybmol) + 3));

                    $value = strpos($block, $sybmol) ? $value : null;

                    if ($attribute_id['filter_type'] === '3') {
                        if (!$value) {
                            continue;
                        }

                        $query .= "&range[{$attribute}][left]={$value}&range[{$attribute}][right]={$value}";
                    } elseif ($attribute_id['filter_type'] === '2') {

                        $query .= "&custom[{$attribute}][]={$value}";
                    } elseif ($attribute_id['filter_type'] === '1') {
                        if (!$value) {
                            continue;
                        }

                        $query .= "&custom[{$attribute}][]={$value}";
                    } else {
                        continue;
                    }
                }
            }

            $price = CloudStore::$app->store->execGet("SELECT MIN(price), MAX(price) FROM products WHERE category = :id AND store = :store", [":id" => $category_id, ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]])[0];

            //$price = \CloudStore\App\Engine\Components\Getter::getFreeData("SELECT MIN(price), MAX(price) FROM products WHERE category_id = ?", [$category_id], false)[0];

            $price_min = (float)$price['MIN(price)'];
            $price_max = (float)$price['MAX(price)'];

            $token = Utils::generateToken();

            $filters[] = [
                'filter_name' => $filter['filter_name'],
                'filter_link' => "?filter_keys=&price[min]={$price_min}&price[max]={$price_max}{$query}&category_name={$category_name}&csrf={$token}"
            ];

            $query = null;
        }

        return $filters;
    }

    public function findAllChildCategories(int $categoryID)
    {
        // @todo find all children categories
    }
}
