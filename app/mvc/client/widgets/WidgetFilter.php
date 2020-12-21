<?php

namespace CloudStore\App\MVC\Client\Widgets;

use CloudStore\App\Engine\Config\Config;
use CloudStore\App\Engine\Core\Widget;
use CloudStore\CloudStore;

/**
 * Class WidgetFilter
 * @package CloudStore\App\MVC\Client\Widgets
 * @deprecated until fixed
 * @todo there are so many things to fix...
 */
class WidgetFilter extends Widget
{

    protected $filter;
    protected $price;
    protected $brands;
    protected $prices;
    protected $keys;

    private $final;
    private $category;
    private $filters;
    private $attributes_info;
    private $availability;

    private $vari;

    public function catalog(string $category = null)
    {

        if (empty($category)) {

            return false;
        }


        // Register styles and scripts

        $this->setStyle('widget_filter');
        $this->setScript('widget_filter');


        $this->filter = $this->getFilter($category);

        $category = $this->getCategory($category);

        $category_id = $category['category_id'];

        $categories = $this->getCategories($category_id);
        $categoriesPlaceholders = implode(",", array_fill(0, count($categories), "?"));
        $storeId = Config::$config["site_id"];
        $params = array_merge($categories, [$storeId]);

        $this->price = CloudStore::$app->store->execGet("SELECT MIN(price), MAX(price) FROM products_category pc INNER JOIN products p ON pc.id = p.id AND pc.store = p.store WHERE pc.category_id IN ({$categoriesPlaceholders}) AND pc.store = ?", $params)[0];

        $this->brands = CloudStore::$app->store->execGet("SELECT DISTINCT brand FROM products_category pc INNER JOIN products p ON pc.id = p.id AND pc.store = p.store WHERE pc.category_id IN ({$categoriesPlaceholders}) AND pc.store = ? AND p.brand <> ''", $params);

        $this->availability = $this->getAvailability($category_id);

        $price_min = (int)$this->price['MIN(price)'];
        $price_min = $price_min <= 100 ? $price_min : $price_min - 100;

        $price_max = (int)$this->price['MAX(price)'] + 100;

        return $this->render("widget_filter_1", [
            "filter" => $this->filter,
            "price" => $this->price,
            "brands" => $this->brands,
            "category" => $category,
            "avail" => $this->availability,
            "price_min" => $price_min,
            "price_max" => $price_max
        ]);
    }

    private function getFilter($category)
    {

        $category = $this->getCategory($category);

        $category_id = $category['category_id'] ?? null;

        $categories = $this->getCategories($category_id);
        $categoriesPlaceholders = implode(",", array_fill(0, count($categories), "?"));
        $storeId = Config::$config["site_id"];
        $params = array_merge($categories, [$storeId]);

        $filters_category = CloudStore::$app->store->execGet("SELECT * FROM sef_filters_category WHERE category_id IN ({$categoriesPlaceholders}) AND store = ? ORDER BY filter_order ASC", $params);
        //$filters_category = CloudStore::$app->store->load("sef_filters_category", ["category_id" => $category_id], ["filter_order" => "ASC"], [], false);

        if (empty($filters_category)) {
            return false;
        }

        $params = array_merge($categories, $categories, [$storeId]);

        $filters = CloudStore::$app->store->execGet("SELECT * FROM sef_filters s 
          INNER JOIN sef_filters_category sf ON s.attribute_name = sf.attribute_name AND s.store = sf.store 
          WHERE sf.category_id IN ({$categoriesPlaceholders}) AND s.id IN (SELECT p.id FROM products_category p WHERE p.category_id IN ({$categoriesPlaceholders})) AND s.filter_active = 1 AND s.value_name <> '' AND s.store = ? 
          ORDER BY s.filter_order ASC ", $params, false);

        /* $filters = \CloudStore\App\Engine\Components\S::execGet( "SELECT * FROM sef_filters s LEFT JOIN sef_filters_category c ON
          s.attribute_name = c.attribute_name AND
          s.store          = c.store AND
          s.category_id    = c.category_id
          WHERE s.category_id = ? AND s.attribute_name IN ({$placeholders}) AND s.filter_active = '1' AND s.value_name <> '' AND s.store = ? ORDER BY c.filter_order ASC ", $this->attributes, false ); */

        if (empty($filters)) {

            return false;
        }

        foreach ($filters_category as $key => $value) {

            $this->attributes_info[$value['attribute_name']] = $value;
        }

        foreach ($filters as $key => $value) {

            // Temporary!
            if (empty($this->attributes_info[$value['attribute_name']])) {
                continue;
            }

            if ($this->attributes_info[$value['attribute_name']]['filter_type'] === "2") {

                if (mb_strtolower(trim($value['value_name'])) !== 'есть' AND mb_strtolower(trim($value['value_name'])) !== 'in stock') {

                    continue;
                }
            }

            $this->filters[$value['attribute_name']]['info'] = $this->attributes_info[$value['attribute_name']];

            if (@in_array($value['value_name'], $this->filters[$value['attribute_name']]['values'])) {

                continue;
            }

            if (empty($value['value_name'])) {

                continue;
            }

            $this->filters[$value['attribute_name']]['values'][] = $value['value_name'];
        }

        if (!empty($this->filters)) {

            // For min and max values of range filter
            $get = Request::get("range");

            foreach ($this->filters as $key => $value) {

                if (empty($value['values'])) {

                    continue;
                }

                if ($value['info']['filter_type'] === "3") {

                    if (isset($get[$this->filters[$key]['info']['attribute_name']])) {

                        $this->filters[$key]['info']['filter_range_min'] = isset($get[$this->filters[$key]['info']['attribute_name']]["left"]) ? $get[$this->filters[$key]['info']['attribute_name']]["left"] : min($value['values']);
                        $this->filters[$key]['info']['filter_range_max'] = isset($get[$this->filters[$key]['info']['attribute_name']]["right"]) ? $get[$this->filters[$key]['info']['attribute_name']]["right"] : max($value['values']);
                    } else {

                        $this->filters[$key]['info']['filter_range_min'] = min($value['values']);
                        $this->filters[$key]['info']['filter_range_max'] = max($value['values']);
                    }

                    $this->filters[$key]['info']['checked'] = !empty($get[$this->filters[$key]['info']['attribute_name']]['checked']) ? 1 : 0;

                }

                $this->final[] = $this->filters[$key];
            }

            return CloudStore::$app->tool->utils->removeSpecialChars($this->final);
        }

        return false;
    }

    public function filter()
    {
        // Register styles and scripts

        $this->setStyle('widget_filter');
        $this->setScript('widget_filter');

        $category = $this->getCategory(Request::get('category_name'));
        $category_id = $category['category_id'];

        $categories = $this->getCategories($category_id);
        $categoriesPlaceholders = implode(",", array_fill(0, count($categories), "?"));
        $storeId = Config::$config["site_id"];
        $params = array_merge($categories, [$storeId]);

        $this->filter = $this->getFilter(Request::get('category_name'));

        $this->prices = CloudStore::$app->store->execGet("SELECT MIN(price), MAX(price) FROM products_category pc INNER JOIN products p ON pc.id = p.id AND pc.store = p.store WHERE pc.category_id IN ({$categoriesPlaceholders}) AND pc.store = ?", $params)[0];

        $this->brands = CloudStore::$app->store->execGet("SELECT DISTINCT brand FROM products_category pc INNER JOIN products p ON pc.id = p.id AND pc.store = p.store WHERE pc.category_id IN ({$categoriesPlaceholders}) AND pc.store = ? AND p.brand <> ''", $params);

        $this->price = Request::get('price');

        $this->keys = Request::get('filter_keys');

        $this->availability = $this->getAvailability($category_id);

        $price_min = (int)$this->prices['MIN(price)'];
        $price_min = $price_min <= 100 ? $price_min : $price_min - 100;

        $price_max = (int)$this->prices['MAX(price)'] + 100;

        return $this->render("widget_filter_2", [
            "filter" => $this->filter,
            "prices" => $this->prices,
            "price" => $this->price,
            "brands" => $this->brands,
            "keys" => $this->keys,
            "category" => $category,
            "avail" => $this->availability,
            "price_min" => $price_min,
            "price_max" => $price_max
        ]);
    }

    public function preparedFilters($category)
    {

        $category = $this->getCategory($category);

        if (!$category) {

            return false;
        }

        $category_id = $category["category_id"];
        $category_handle = $category["category_handle"];

        $filters = $this->getPreparedFilters($category_id, $category_handle);

        return $this->render("widget_filter_prepared", [
            "prepared_filters" => $filters
        ]);
    }

    public function getPreparedFilters($category_id, $category_name)
    {
        return [];
        $query = null;
        $filters = [];

        $prepared_filters = \CloudStore\App\Engine\Components\S::load("sef_prepared_filters", ["filter_category" => $category_id]);

        if (!$prepared_filters) {
            return false;
        }

        foreach ($prepared_filters as $filter) {
            $blocks = explode(PHP_EOL, $filter['filter_value']);

            $sybmol = " - ";

            if (!empty($blocks)) {
                foreach ($blocks as $block) {

                    $attribute = strpos($block, $sybmol) ? trim(substr($block, 0, strpos($block, $sybmol))) : $block;

                    $attribute_id = \CloudStore\App\Engine\Components\S::loadOne("sef_filters_category", ["attribute_name" => $attribute, "category_id" => $category_id]);

                    $value = trim(substr($block, strpos($block, $sybmol) + 3));

                    $value = strpos($block, $sybmol) ? $value : null;

                    if ($attribute_id['filter_type'] === '3') {
                        if (!$value) {
                            continue;
                        }

                        $query .= "&range[{$attribute_id['attribute_name']}][left]={$value}&range[{$attribute_id['attribute_name']}][right]={$value}";
                    } elseif ($attribute_id['filter_type'] === '2') {

                        $sub_values = \CloudStore\App\Engine\Components\S::execGet("SELECT * FROM sef_filters WHERE attribute_name = :id AND BINARY value_name = :name AND store = :store", [
                            ":id" => $attribute_id['attribute_name'],
                            ":name" => $value,
                            ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]
                        ]);

                        foreach ($sub_values as $sub_value) {
                            $query .= "&custom[{$attribute_id['attribute_name']}][]={$sub_value['value_name']}";
                        }
                    } elseif ($attribute_id['filter_type'] === '1') {
                        if (!$value) {
                            continue;
                        }

                        $value_id = \CloudStore\App\Engine\Components\S::execGet("SELECT * FROM sef_filters WHERE attribute_name = :id AND BINARY value_name = :name AND store = :store", [
                            ":id" => $attribute_id['attribute_name'],
                            ":name" => $value,
                            ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]
                        ]);

                        if (!$value_id) {
                            continue;
                        }

                        $value_id = $value_id[0]["value_name"];

                        $query .= "&custom[{$attribute_id['attribute_name']}][]={$value_id}";
                    } else {
                        continue;
                    }
                }
            }

            $price = \CloudStore\App\Engine\Components\S::execGet("SELECT MIN(price), MAX(price) FROM products_category pc INNER JOIN products p ON pc.id = p.id AND pc.store = p.store WHERE pc.category_id = :id AND pc.store = :store", [
                "id" => $category_id, ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]
            ])[0];

            $price_min = (float)$price['MIN(price)'];
            $price_max = (float)$price['MAX(price)'];

            $token = \CloudStore\App\Engine\Components\Utils::generateToken();

            $filters[] = [
                'filter_name' => $filter['filter_name'],
                'filter_link' => "?filter_keys=&price[min]={$price_min}&price[max]={$price_max}{$query}&category_name={$category_name}&csrf={$token}"
            ];

            $query = null;
        }

        return $filters;
    }

    private function getAvailability($category): array
    {

        // @todo optimize it!!!
        // +2 query
        // Combine it?

        return [];
        $categories = $this->getCategories($category);
        $categoriesPlaceholders = implode(",", array_fill(0, count($categories), "?"));
        $storeId = Config::$config["site_id"];
        $params = array_merge($categories, [$storeId]);

        // Select products by different inventory parameters
        $sql = "SELECT SUM(p.quantity_available), SUM(p.quantity_stock), SUM(p.quantity_possible), 
                SUM(i.quantity_available), SUM(i.quantity_stock), SUM(i.quantity_possible) FROM products_category pc 
                INNER JOIN products p ON pc.id = p.id AND pc.store = p.store
                LEFT JOIN products_inventory i ON p.id = i.product AND p.store = i.store
                WHERE pc.category_id IN ({$categoriesPlaceholders}) AND avail <> 0 AND p.price <> 0 AND title <> '' AND pc.store = ?";

        $products = CloudStore::$app->store->execGet($sql, $params);

        if (!$products) {

            return array();
        }

        $products = $products[0];
        $array = array();


        if ($products["SUM(p.quantity_available)"] || $products["SUM(i.quantity_available)"]) {

            $array["quantity_available"] = true;
        }

        if ($products["SUM(p.quantity_stock)"] || $products["SUM(i.quantity_stock)"]) {

            $array["quantity_stock"] = true;
        }

        if ($products["SUM(p.quantity_possible)"] || $products["SUM(i.quantity_possible)"]) {

            $array["quantity_possible"] = true;
        }

        // Then select products that is not in stock
        $sql = "SELECT COUNT(p.id) FROM products_category pc 
                INNER JOIN products p ON pc.id = p.id AND pc.store = p.store
                LEFT JOIN products_inventory i ON p.id = i.product AND p.store = i.store 
                WHERE (i.quantity_available < 1 AND i.quantity_stock < 1 AND i.quantity_possible < 1 ) OR (p.quantity_available < 1 AND p.quantity_stock < 1 AND p.quantity_possible < 1) AND p.avail <> 0 AND p.price <> 0 AND p.title <> '' AND pc.category_id IN ({$categoriesPlaceholders}) AND pc.store = ?";

        $products = CloudStore::$app->store->execGet($sql, $params);

        if (!$products) {

            return $array;
        }

        $products = $products[0];
        if ($products["COUNT(p.id)"]) {

            $array["none"] = true;
        }

        return $array;
    }

    protected function getCategory($handle)
    {
        if (!$this->category) {

            $this->category = CloudStore::$app->store->loadOne("category", ["category_handle" => $handle]);
        }

        return $this->category;
    }

    private function getCategories($category): array
    {
        if (!$category) {
            return [];
        }

        $children = CloudStore::$app->store->load("category", ["parent" => $category]);
        if (!$children) {
            return [$category];
        }

        foreach ($children as $child) {
            $final = array_merge([$category], $this->getCategories($child["category_id"]));
        }

        return $final;
    }
}
