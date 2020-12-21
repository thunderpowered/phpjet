<?php

namespace CloudStore\App\Engine\Ajax\Handlers;

use CloudStore\App\Engine\Components\Request;
use CloudStore\App\Engine\Config\Database;
use CloudStore\App\Engine\Core\System;

class AjaxCart
{

    public function load_cart()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Здесь должен проверяться токен, но пока этого нет
//            $csrf = \CloudStore\App\Engine\Components\Request::post('csrf');
//            if (!\CloudStore\App\Engine\Components\Utils::validateToken($csrf)) {
//                return json_encode([
//                    'exist' => false
//                ]);
//            }

            $ip = \CloudStore\App\Engine\Core\System::getUserIP();
            $id = Request::post("id");

            if ($id) {

                $array = \CloudStore\App\Engine\Components\S::load("cart", ["cart_ip" => $ip, "cart_id" => $id]);
            } else {

                $array = \CloudStore\App\Engine\Components\S::load("cart", ["cart_ip" => $ip]);
            }

            $count = 0;
            $sum = 0;

            if ($array) {

                foreach ($array as $cur) {
                    $count += $cur['cart_count'];
                    $sum += $cur['cart_price'];
                }
            }

            $sum = Utils::asPrice($sum);

            if ($count > 0) {

                return json_encode([
                    'exist' => true,
                    'count' => $count,
                    'sum' => $sum
                ]);
            } else {
                return json_encode([
                    'exist' => false
                ]);
            }
        }

        return json_encode([
            'exist' => false
        ]);
    }

    public function add_to_cart()
    {
        $db = \CloudStore\App\Engine\Config\Database::getInstance();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Getting info
            $handle = \CloudStore\App\Engine\Components\Utils::removeSpecialChars($_POST['hand']);
            $csrf = \CloudStore\App\Engine\Components\Utils::removeSpecialChars($_POST['csrf']);
            $count = \CloudStore\App\Engine\Components\Utils::removeSpecialChars($_POST['coun']);
            $ip = \CloudStore\App\Engine\Core\System::getUserIP();

            $modification = (int)Request::post("modification");

            if (!\CloudStore\App\Engine\Components\Utils::validateToken($csrf)) {
                return false;
            }

            // If smth wrong
            if ($count < 1 OR $handle === NULL) {
                return 0;
            }

            $product = \CloudStore\App\Engine\Components\S::loadOne("products", ["handle" => $handle]);
            if (!$product) {

                return 0;
            }

            if ($modification !== 0) {

                $inventory = \CloudStore\App\Engine\Components\S::loadOne("products_inventory", ["id" => $modification, "product" => $product["id"]]);

                if ($inventory) {

                    $product['quantity_available'] = $inventory["quantity_available"];
                    $product['quantity_stock'] = $inventory["quantity_stock"];
                    $product['quantity_possible'] = $inventory["quantity_possible"];
                    $product["price"] = $inventory["price"];
                } else {

                    $modification = 0;
                }
            }

            $price = $product['price'];
            $all_count = $product['quantity_available'];
            $quant = $product['quantity_stock'];
            $possible = $product['quantity_possible'];

            if ($all_count <= (0 - ($quant + $possible))) {
                return false;
            }

            $products = \CloudStore\App\Engine\Components\S::load("cart", ["cart_ip" => $ip, "id" => $product["id"], "products_modification" => $modification]);
            $stmt = null;
            // If product already exist 

            if ($products AND count($products) >= 1) {

                $ex_count = $products[0]['cart_count'];
                $full_count = $product['quantity_available'] - ($count + $ex_count);

                if ($full_count < (0 - ($quant + $possible))) {
                    $total = $product['quantity_available'] - (0 - ($quant + $possible));
                } else {
                    $total = $products[0]['cart_count'] + $count;
                }

                $id = $products[0]['cart_id'];
                $sum = $total * $price;

                if (\CloudStore\App\Engine\Components\S::update("cart", ["cart_count" => $total, "cart_price" => $sum], ["cart_id" => $id])) {
                    // @todo return json!
                    return $id;
                }
            } else {

                $full_count = $product['quantity_available'] - $count;

                if ($full_count < (0 - ($quant + $possible))) {

                    $total = $product['quantity_available'] - (0 - ($quant + $possible));
                } else {
                    $total = $count;
                }

                $sum = $total * $price;

                $result = \CloudStore\App\Engine\Components\S::collect("cart", ["products_handle" => $product["handle"], "id" => $product["id"], "products_modification" => $modification, "cart_price" => $sum, "cart_count" => $total, "cart_ip" => $ip]);

                $id = $db->lastInsertId();

                if ($result) {
                    return $id;
                }
            }

            return 0;
        }

        return 0;
    }

    public function count_minus()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            //$db = Database::getInstance();

            $id = \CloudStore\App\Engine\Components\Utils::removeSpecialChars($_POST['id']);
            //$count = \CloudStore\App\Engine\Components\Utils::clear($_POST['count']);
            $csrf = \CloudStore\App\Engine\Components\Utils::removeSpecialChars($_POST['csrf']);
            $ip = \CloudStore\App\Engine\Core\System::getUserIP();

            if (!\CloudStore\App\Engine\Components\Utils::validateToken($csrf)) {
                return false;
            }

            $array = \CloudStore\App\Engine\Components\S::execGet("SELECT c.cart_count, c.cart_price, c.products_modification, p.id, p.price, p.quantity_stock, p.quantity_available, p.quantity_possible FROM cart c INNER JOIN products p ON c.id = p.id AND c.store = p.store WHERE cart_id=:id AND cart_ip=:ip AND c.store = :store", [
                ":id" => $id, ":ip" => $ip, ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]
            ])[0];

            $inventory = \CloudStore\App\Engine\Components\S::loadOne("products_inventory", ["id" => $array["products_modification"], "product" => $array["id"]]);

            if ($inventory) {

                $array['quantity_available'] = $inventory["quantity_available"];
                $array['quantity_stock'] = $inventory["quantity_stock"];
                $array['quantity_possible'] = $inventory["quantity_possible"];
                $array['price'] = $inventory["price"];
            }

            if (count($array) > 0) {
                $count = $array['cart_count'] - 1;

                $ex_count = $array['cart_count'];
                $full_count = $array['quantity_available'] - $count;

                if ($full_count < (0 - ($array['quantity_stock'] + $array['quantity_possible']))) {
                    $total = $array['quantity_available'] - (0 - ($array['quantity_stock'] + $array['quantity_possible']));
                } else {
                    $total = $count;
                }

                if ($total <= 0) {
                    $total = 1;
                }

                $sum = $total * $array['price'];

                if (\CloudStore\App\Engine\Components\S::update("cart", ["cart_count" => $total, "cart_price" => $sum], ["cart_id" => $id, "cart_ip" => $ip])) {

                    return $total;
                } else {

                    return 0;
                }
            }

            return 0;
        }

        return 0;
    }

    public function count_plus()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $id = \CloudStore\App\Engine\Components\Utils::removeSpecialChars($_POST['id']);
            $count = \CloudStore\App\Engine\Components\Utils::removeSpecialChars($_POST['count']);
            $csrf = \CloudStore\App\Engine\Components\Utils::removeSpecialChars($_POST['csrf']);
            $ip = \CloudStore\App\Engine\Core\System::getUserIP();

            if (!\CloudStore\App\Engine\Components\Utils::validateToken($csrf)) {
                return false;
            }

            $array = \CloudStore\App\Engine\Components\S::execGet("SELECT c.cart_count, c.cart_price, p.price, p.quantity_stock, c.products_modification, p.id, p.quantity_available, p.quantity_possible FROM cart c LEFT OUTER JOIN products p ON c.id = p.id AND c.store = p.store WHERE cart_id=:id AND cart_ip=:ip AND c.store = :store", [
                ":id" => $id, ":ip" => $ip, ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]
            ])[0];

            $inventory = \CloudStore\App\Engine\Components\S::loadOne("products_inventory", ["id" => $array["products_modification"], "product" => $array["id"]]);

            if ($inventory) {

                $array['quantity_available'] = $inventory["quantity_available"];
                $array['quantity_stock'] = $inventory["quantity_stock"];
                $array['quantity_possible'] = $inventory["quantity_possible"];
            }

            $inventory = \CloudStore\App\Engine\Components\S::loadOne("products_inventory", ["id" => $array["products_modification"], "product" => $array["id"]]);

            if ($inventory) {

                $array['quantity_available'] = $inventory["quantity_available"];
                $array['quantity_stock'] = $inventory["quantity_stock"];
                $array['quantity_possible'] = $inventory["quantity_possible"];
                $array['price'] = $inventory["price"];
            }

            if (count($array) > 0) {

                $count = $array['cart_count'] + 1;

                $ex_count = $array['cart_count'];
                $full_count = $array['quantity_available'] - $count;

                if ($full_count < (0 - ($array['quantity_stock'] + $array['quantity_possible']))) {
                    $total = $array['quantity_available'] - (0 - ($array['quantity_stock'] + $array['quantity_possible']));
                } else {
                    $total = $count;
                }

                if ($total <= 0) {
                    $total = 0;
                }

                $sum = $total * $array['price'];

                if (\CloudStore\App\Engine\Components\S::update("cart", ["cart_count" => $total, "cart_price" => $sum], ["cart_id" => $id, "cart_ip" => $ip])) {
                    return $total;
                } else {
                    return 0;
                }
            }

            return 0;
        }

        return 0;
    }

    public function count_change()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $db = Database::getInstance();

            $id = \CloudStore\App\Engine\Components\Utils::removeSpecialChars($_POST['id']);
            $count = (int)\CloudStore\App\Engine\Components\Utils::removeSpecialChars($_POST['count']);
            $csrf = \CloudStore\App\Engine\Components\Utils::removeSpecialChars($_POST['csrf']);
            $ip = \CloudStore\App\Engine\Core\System::getUserIP();

            if ($count === 0 OR $count < 0) {
                $count = 1;
            }

            if (!\CloudStore\App\Engine\Components\Utils::validateToken($csrf)) {
                return false;
            }

            $array = \CloudStore\App\Engine\Components\S::execGet("SELECT c.cart_count, c.cart_price, p.price, p.quantity_stock, c.products_modification, p.id, p.quantity_available, p.quantity_possible FROM cart c LEFT OUTER JOIN products p ON c.id = p.id AND c.store = p.store WHERE cart_id=:id AND cart_ip=:ip AND c.store = :store", [
                ":id" => $id, ":ip" => $ip, ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]
            ])[0];

            $inventory = \CloudStore\App\Engine\Components\S::loadOne("products_inventory", ["id" => $array["products_modification"], "product" => $array["id"]]);

            if ($inventory) {

                $array['quantity_available'] = $inventory["quantity_available"];
                $array['quantity_stock'] = $inventory["quantity_stock"];
                $array['quantity_possible'] = $inventory["quantity_possible"];
                $array['price'] = $inventory["price"];
            }

            if (count($array) > 0) {

                $full_count = $array['quantity_available'] - $count;

                if ($full_count < (0 - ($array['quantity_stock'] + $array['quantity_possible']))) {
                    $total = $array['quantity_available'] - (0 - ($array['quantity_stock'] + $array['quantity_possible']));
                } else {
                    $total = $count;
                }

                if ($total <= 0) {
                    $total = 1;
                }


                $sum = $total * $array['price'];

                if (\CloudStore\App\Engine\Components\S::update("cart", ["cart_count" => $total, "cart_price" => $sum], ["cart_id" => $id, "cart_ip" => $ip])) {
                    return 1;
                } else {
                    return 0;
                }
            }

            return 0;
        }

        return 0;
    }

    public function delete_cart()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $db = Database::getInstance();

            $post = json_decode(\CloudStore\App\Engine\Components\Request::Post('post'));

            if (!$post) {

                return json_encode(["status" => false]);
            }

            $id = $post->id;
            $csrf = $post->csrf;

            if (!\CloudStore\App\Engine\Components\Utils::validateToken($csrf)) {

                return json_encode(["status" => false]);
            }

            $ip = \CloudStore\App\Engine\Core\System::getUserIP();

            $prod = \CloudStore\App\Engine\Components\S::loadOne("cart", ["cart_id" => $id, "cart_ip" => $ip]);

            if (!$prod) {

                return json_encode(["status" => false]);
            }

            $handle = $prod['products_handle'];


            $result1 = \CloudStore\App\Engine\Components\S::delete("cart", ["cart_id" => $id, "cart_ip" => $ip]);

            $result2 = \CloudStore\App\Engine\Components\S::delete("order_products", ["products_handle" => $handle, "orders_ip" => $ip, "orders_status" => 0, "products_modification" => $prod["products_modification"]]);

            if ($result1 AND $result2) {
                return json_encode(["status" => true]);
            }

            return json_encode(["status" => true]);
        }

        return json_encode(["status" => false]);
    }
}
