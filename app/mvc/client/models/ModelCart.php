<?php

namespace CloudStore\App\MVC\Client\Models;

use CloudStore\App\Engine\ActiveRecord\Tables\Cart;
use CloudStore\App\Engine\Config\Database;
use CloudStore\App\Engine\Core\Model;
use CloudStore\App\Engine\Core\System;

/**
 * Class ModelCart
 * @package CloudStore\App\MVC\Client\Models
 */
class ModelCart extends Model
{
    /**
     * ModelCart constructor.
     * @param string $name
     */
    public function __construct(string $name = "")
    {
        parent::__construct($name);
    }

    public function prepareCheckout(string $ip)
    {
        $cartItems = Cart::get(["ip" => $ip]);
        foreach ($cartItems as $cartItem) {

        }

        return $cartItems;


        $db = Database::getInstance();

        $array = CloudStore::$app->store->load("cart", ["cart_ip" => $ip]);

        if (!$array) {
            return false;
        }

        foreach ($array as $cur) {

            $temp = CloudStore::$app->store->load("order_products", [
                "orders_ip" => $ip,
                "id" => $cur['id'],
                "orders_status" => 0,
                "products_modification" => $cur["products_modification"]
            ]);

            if ($temp) {

                CloudStore::$app->store->update("order_products", [
                    "orders_count" => $cur['cart_count'],
                    "orders_price" => $cur['cart_price']
                ], [
                    "id" => $cur['id'],
                    "orders_ip" => $ip
                ]);
            } else {

                CloudStore::$app->store->collect("order_products", [
                    "products_handle" => $cur['products_handle'],
                    "orders_price" => $cur['cart_price'],
                    "orders_count" => $cur['cart_count'],
                    "orders_ip" => $ip,
                    "products_modification" => $cur["products_modification"],
                    "orders_cart_id" => $cur["cart_id"],
                    "id" => $cur["id"]
                ]);
            }
        }

        // Note
        Request::setSession('note', Request::post('note'));
        return true;
    }

    public function getCart()
    {

        $ip = System::getUserIP();
        $sql = "SELECT c.cart_id, c.cart_price, c.cart_count, p.id, p.quantity_available, c.products_modification, p.brand, p.title, p.price, p.handle, p.image, p.quantity_stock, p.products_shipping_date, p.quantity_possible, p.products_possible_shipping_date, p.products_shipping_expectation "
            . "FROM cart c "
            . "INNER JOIN products p ON c.id = p.id AND c.store = p.store "
            . "LEFT JOIN products_inventory pi ON c.products_modification = pi.id AND c.store = pi.store "
            . "WHERE cart_ip=:ip AND c.store = :store AND p.title <> '' ";
        $array = CloudStore::$app->store->execGet($sql, [":ip" => $ip, ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]]);


        if (!$array) {
            return Utils::homeRedirect();
        }

        for ($i = 0, $c = count($array); $i < $c; $i++) {

            // Yeah, i know it's not good. Sorry.
            if ($array[$i]["products_modification"] !== "0" && $modification = CloudStore::$app->store->loadOne("products_inventory", ["product" => $array[$i]["id"], "id" => $array[$i]["products_modification"]])) {

                $array[$i]['status'] = ProductManager::getStatus($modification['quantity_available'], $modification['quantity_stock'], $modification['products_shipping_date'], $modification['quantity_possible'], $modification['products_possible_shipping_date'], $array[$i]['products_shipping_expectation'], $array[$i]['id']);
                $array[$i]["title"] = $array[$i]["title"] . " [" . $modification["modification"] . "]";
            } else {

                $array[$i]['status'] = ProductManager::getStatus($array[$i]['quantity_available'], $array[$i]['quantity_stock'], $array[$i]['products_shipping_date'], $array[$i]['quantity_possible'], $array[$i]['products_possible_shipping_date'], $array[$i]['products_shipping_expectation'], $array[$i]['id']);
            }

            if (!$array[$i]['status']["shipping_avail"]) {
                CloudStore::$app->store->delete("cart", ["cart_id" => $array[$i]["cart_id"]]);
                unset($array[$i]);
            }
        }

        return $array;
    }

    public function getSum($products)
    {

        $sum = 0;
        if (!$products) {
            return 0;
        }

        foreach ($products as $cur) {
            $sum = $sum + $cur['cart_price'];
        }

        return Utils::asPrice($sum);
    }
}
