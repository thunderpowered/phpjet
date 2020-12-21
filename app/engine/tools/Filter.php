<?php

namespace CloudStore\App\Engine\Tools;

use CloudStore\App\Engine\Config\Config;
use CloudStore\App\Engine\Core\Component;
use CloudStore\App\Engine\Core\System;

/**
 *
 * Component: ShopEngine Filter
 * Description: ShopEngine has a built-in filter engine. I'll add description later.
 *
 *
 */
class Filter extends Component
{

    private static $param;
    private static $products;

    public static function getProducts()
    {

        $route = Router::getRoute(false);
        if (isset($route[2])) {

            $string = $route[2];
        }

        $token = Request::get("csrf");

        //Utils::validateAction($token);

        return Filter::getFromString($string, 12);
    }

    public static function getFromString($string, $num = 8)
    {

        if (Filter::$products) {

            return Filter::$products;
        }

        /**
         *
         * I know it looks complicated. I'll try to simplify it later.
         * @todo simplify function
         *
         */
        $variables = array();
        parse_str($string, $variables);

        $variables = Utils::removeSpecialChars($variables);

        $str = null;
        $brand_str = null;
        $brands = $variables['static']['brands'] ?? "";

        if (!empty($variables['custom']) OR
            !empty($variables['single']) OR
            (!empty($variables['range']) && $variables['range_check'])) {

            $custom = $variables['custom'] ?? null;
            $single = $variables['single'] ?? null;
            $range = $variables['range'] ?? null;
            $inventory = $variables['inventory'] ?? null;

            //It starts with custom...

            $first = null;
            $temp = null;

            if (!empty($custom)) {

                foreach ($custom as $key => $value) {

                    $sub_first = null;

                    $placeholders = implode(',', array_fill(0, count($value), '?'));

                    $sub_temp = "f.value_name IN ({$placeholders})";

                    foreach ($value as $sub_key => $sub_value) {
                        Filter::$param[] = $sub_value;
                    }

                    $temp .= $first . "(({$sub_temp}) AND f.attribute_name = ? AND f.filter_active = '1')";
                    Filter::$param[] = $key;

                    $first = " OR ";
                }
            }

            //Signe
            if (!empty($single)) {

                foreach ($single as $key => $value) {

                    $temp .= $first . " (f.attribute_name = ? AND (f.value_name = 'есть' OR f.value_name = 'in stock') AND f.filter_active = '1')";
                    Filter::$param[] = $key;

                    $first = " OR ";
                }
            }

            //Range
            if (!empty($range)) {

                foreach ($range as $key => $value) {

                    // If it wasn't changed
                    if (!$value["checked"]) {

                        unset($range[$key]);
                        continue;
                    }

                    $sub_first = null;
                    $sub_temp = null;

                    $temp .= $first . " (f.attribute_name = ? AND f.filter_active = '1' AND (1 * f.value_name) BETWEEN ? AND ?)";
                    Filter::$param[] = $key;
                    Filter::$param[] = $value['left'];
                    Filter::$param[] = $value['right'];

                    $first = " OR";
                }
            }

            //Category
            $category = $variables["category_name"] ?? null;

            if (empty($category)) {

                return false;
            }

            // Inventory
            $inventory_str = Filter::getInventory($inventory);

            $category_id = CloudStore::$app->store->loadOne("category", ["category_handle" => $category])['category_id'];
            $categories = Filter::getCategories($category_id);
            $categoriesPlaceholders = implode(",", array_fill(0, count($categories), "?"));

            Filter::$param = array_merge(Filter::$param, $categories);
            Filter::$param[] = Config::$config["site_id"];

            if ($temp && $inventory_str) {

                $temp = $temp . " AND ";
            }

            $sql = "SELECT f.id, COUNT(*) FROM sef_filters f 
                    INNER JOIN sef_filters_category sf ON f.attribute_name = sf.attribute_name AND f.store = sf.store 
                    INNER JOIN products p ON f.id = p.id
                    INNER JOIN products_category pc ON p.id = pc.id AND sf.category_id = pc.category_id
                    LEFT JOIN products_inventory i ON f.id = i.product AND f.store = i.store 
                    WHERE {$temp}{$inventory_str} AND sf.category_id IN ({$categoriesPlaceholders}) AND f.store = ? GROUP BY f.id";

            /*$sql = "SELECT f.id, COUNT(*) FROM sef_filters f
                    INNER JOIN sef_filters_category sf ON f.attribute_name = sf.attribute_name AND f.store = sf.store 
                    INNER JOIN products p ON f.id = p.id AND f.store = p.store
                    WHERE {$temp} AND sf.category_id = ? AND f.store = ? GROUP BY f.id";*/

            $sql = preg_replace('/ {2,}/', ' ', $sql);

            $array = \CloudStore\App\Engine\Tools\S::execGet($sql, Filter::$param, false);

            $count = 0;

            $count = (!empty($custom) ? Utils::arrayCountLower($custom) : 0) + (!empty($single) ? count($single) : 0) + (!empty($range) ? count($range) : 0);
            
            if (empty($array)) {

                return false;
            }

            //$count = count($custom);

            $not_first = null;
            $_temp = $temp;
            $temp = null;

            foreach ($array as $cur) {
                // TEMPORARY!
                if ((int)$cur['COUNT(*)'] === $count || !$_temp) {
                //if (true) {
                    $IDs[] = $cur['id'];
                    $temp .= $not_first . " ?";
                    $not_first = ",";
                }
            }

            Filter::$param = [];

            //Price, keys and brands

            $price = $variables['price'] ?? null;
            if (!empty($price)) {

                Filter::$param[] = $price["min"];
                Filter::$param[] = $price["max"];

                $price = "AND price BETWEEN ? AND ?";
            }

            $filter_start = null;

            $keys = $variables['?filter_keys'] ?? $variables['?filter_keys'] ?? $variables["filter_prepare?filter_keys"] ?? null;

            if ($keys !== "") {
                $keys = explode(',', $keys);

                $str .= "AND (";
                for ($i = 0; $i < count($keys); $i++) {

                    $key = trim($keys[$i]);

                    $str .= $filter_start . "(title LIKE ? OR brand LIKE ?)";
                    Filter::$param[] = "%" . $key . "%";
                    Filter::$param[] = "%" . $key . "%";

                    $filter_start = " OR ";
                }

                $str .= ")";

                $keys = $str;
            }

            if (!empty($brands) AND count($brands) >= 1) {
                $key_start = "";
                $brand_str = " AND ";
                $brand_str .= "(";

                for ($i = 0; $i < count($brands); $i++) {

                    if ($brands[$i] === "")
                        continue;

                    $key = trim($brands[$i]);

                    $brand_str .= $key_start . "brand LIKE ?";
                    Filter::$param[] = "%" . $key . "%";

                    $key_start = " OR ";
                }

                $brand_str .= ")";
            }

            //OLD 

            if (!empty($IDs)) {

                $sql = "SELECT * FROM products WHERE id IN ({$temp}) {$price} {$keys} {$brand_str} AND store = ? ";

                Filter::$param[] = \CloudStore\App\Engine\Config\Config::$config["site_id"];

                $sql = preg_replace('/ {2,}/', ' ', $sql);

                $param = array_merge($IDs, Filter::$param);

                Filter::$products = ProductManager::loadExec($sql, $param, $num);
                return Filter::$products;
            }
        } else {
            $brands = $variables['static']['brands'] ?? null;
            $price = $variables['price'] ?? null;
            $inventory = $variables['inventory'] ?? null;

            if (!empty($variables['category_name'])) {

                $cat = CloudStore::$app->store->loadOne("category", ["category_handle" => $variables['category_name']], false)['category_id'];
            }

            $keys = null;

            if (!empty($variables['?filter_keys'])) {
                $keys = explode(',', $variables['?filter_keys']);
            } else if (!empty($variables['filter_keys'])) {
                $keys = explode(',', $variables['filter_keys']);
            } else if (!empty($variables['filter_prepare?filter_keys'])) {
                $keys = explode(',', $variables['filter_prepare?filter_keys']);
            }

            $filter_start = null;

            $param = [];
            $query = null;
            $str = null;

            if (!empty($price)) {

                $query .= $filter_start . " p.price BETWEEN ? AND ?";
                $param[] = (float)$price['min'];
                $param[] = (float)$price['max'];
                $filter_start = " AND";
            }
            if (!empty($cat)) {

                $categories = Filter::getCategories($cat);
                $categoriesPlaceholders = implode(",", array_fill(0, count($categories), "?"));

                $query .= $filter_start . " pc.category_id IN ($categoriesPlaceholders)";
                $param = array_merge($param, $categories);
                $filter_start = " AND";
            }

            $start = (!empty($cat) OR !empty($price)) ? "AND" : "";
            $filter_start = "";

            if (!empty($keys)) {

                $str .= $start . " (";

                for ($i = 0; $i < count($keys); $i++) {

                    $key = trim($keys[$i]);

                    $str .= $filter_start . "(p.title LIKE ? OR p.brand LIKE ?)";
                    $param[] = "%" . $key . "%";
                    $param[] = "%" . $key . "%";

                    $filter_start = " OR ";
                }

                $str .= ")";
            }


            //Set brand
            if (!empty($brands) AND count($brands) >= 1) {
                $key_start = "";
                $brand_str = " AND ";
                $brand_str .= "(";

                for ($i = 0; $i < count($brands); $i++) {

                    if ($brands[$i] === "")
                        continue;

                    $key = trim($brands[$i]);

                    $brand_str .= $key_start . "p.brand LIKE ?";
                    $param[] = "%" . $key . "%";

                    $key_start = " OR ";
                }

                $brand_str .= ")";
            }

            $inventory_str = Filter::getInventory($inventory);

            if (empty($query) && empty($str) && empty($brand_str)) {

                return false;
            }

            if (($brand_str || $str || $query) && $inventory_str) {

                $inventory_str = " AND " . $inventory_str;
            }

            $sql = "SELECT p.id, COUNT(p.id) FROM products_category pc 
                    INNER JOIN products p ON pc.id = p.id AND pc.store = p.store 
                    LEFT JOIN products_inventory i ON p.id = i.product AND p.store = i.store
                    WHERE {$query} {$str} {$brand_str} {$inventory_str} AND p.title <> '' AND p.avail = '1' AND pc.store = ? GROUP BY p.id ";

            $param[] = \CloudStore\App\Engine\Config\Config::$config["site_id"];
            $array = CloudStore::$app->store->execGet($sql, $param);

            $IDs = [];
            $temp = "";
            $not_first = "";

            if(!$array) {

                return [];
            }

            foreach ($array as $cur) {
                // TEMPORARY!
                $IDs[] = $cur['id'];
                $temp .= $not_first . " ?";
                $not_first = ",";
            }

            $sql = "SELECT * FROM products WHERE id IN ($temp) AND store = ? ";
            $IDs[] = Config::$config["site_id"];

            Filter::$products = ProductManager::loadExec($sql, $IDs, $num);
            return Filter::$products;
        }

        return [];
    }

    // Helpers
    public static function getInventory($inventory = null)
    {

        if (!$inventory) {

            return false;
        }

        $invent_str = " ( ";
        $fw = null;

        if (in_array("available", $inventory)) {

            $invent_str .= $fw . " (i.quantity_available > 0 OR p.quantity_available > 0)";

            $fw = " OR";
        }

        if (in_array("shipping", $inventory)) {

            $invent_str .= $fw . " (i.quantity_stock > 0 OR p.quantity_stock > 0) ";

            $fw = " OR";
        }

        if (in_array("possible", $inventory)) {

            $invent_str .= $fw . " (i.quantity_possible > 0 OR p.quantity_possible > 0)";

            $fw = " OR";
        }

        if (in_array("none", $inventory)) {

            $invent_str .= $fw . " (( i.quantity_available < 1 AND i.quantity_stock < 1 AND i.quantity_possible < 1 ) OR (p.quantity_available < 1 AND p.quantity_stock < 1 AND p.quantity_possible < 1))";

            $fw = " OR";
        }

        if (!$fw) {

            return false;
        }

        $invent_str .= " ) ";

        return $invent_str;
    }

    private static function getCategories($category) : array
    {
        if (!$category) {
            return [];
        }

        $children = CloudStore::$app->store->load("category", ["parent" => $category]);
        if (!$children) {
            return [$category];
        }

        foreach ($children as $child) {
            $final = array_merge([$category], Filter::getCategories($child["category_id"]));
        }

        return $final;
    }
}
