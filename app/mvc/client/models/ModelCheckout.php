<?php

namespace CloudStore\App\MVC\Client\Models;

use CloudStore\App\Engine\Components\Business;
use CloudStore\App\Engine\Components\S;
use CloudStore\App\Engine\Config\Database;
use CloudStore\App\Engine\Core\Model;
use CloudStore\App\Engine\Core\Router;
use CloudStore\App\Engine\Core\System;
use CloudStore\App\Engine\Components\Utils;

class ModelCheckout extends Model
{

    public $errors = [];
    public $array = [];
    public $id;
    public $key;
    public $shipper_name;
    public $shipper_price;

    public function getPayment($products)
    {
        $full = $this->calculateFullPrice($products, true);

        $products_price = $full['products_price'];
        $shipper_price = $full['shipper_price'];

        $payments = CloudStore::$app->store->load("payment");

        for ($i = 0, $c = count($payments); $i < $c; $i++) {

            if ($payments[$i]['payment_charge_selector'] === '0') {

                $price = $products_price + $shipper_price;

                if (strpos($payments[$i]['payment_charge_value'], "%")) {
                    $charge = $payments[$i]['payment_charge_value'] ? intval($payments[$i]['payment_charge_value']) : 0;

                    $payments[$i]['payment_full_price'] = $price + ($price / 100 * $charge);
                    $payments[$i]['payment_full_charge'] = $price / 100 * $charge;
                } else {

                    $charge = $payments[$i]['payment_charge_value'] ? intval($payments[$i]['payment_charge_value']) : 0;

                    $payments[$i]['payment_full_price'] = $price + $charge;
                    $payments[$i]['payment_full_charge'] = $charge;
                }

                $payments[$i]['payment_full_charge_selector'] = 'no';
            } else {
                $price = $products_price;

                if (strpos($payments[$i]['payment_charge_value'], "%")) {
                    $charge = $payments[$i]['payment_charge_value'] ? intval($payments[$i]['payment_charge_value']) : 0;

                    $payments[$i]['payment_full_price'] = $price + ($price / 100 * $charge) + $shipper_price;
                    $payments[$i]['payment_full_charge'] = $price / 100 * $charge;
                } else {

                    $charge = $payments[$i]['payment_charge_value'] ? intval($payments[$i]['payment_charge_value']) : 0;

                    $payments[$i]['payment_full_price'] = $price + $charge + $shipper_price;
                    $payments[$i]['payment_full_charge'] = $charge;
                }

                $payments[$i]['payment_full_charge_selector'] = 'yes';
            }
        }

        return $payments;
    }

    public function calculateFullPrice($products, $separate = false)
    {
        $full = $this->setShipper();

        $this->shipper_name = $full['shipper_type'];
        $this->shipper_price = $full['full_cost'];
        $final = $this->getProductsPrice($products);
        if (is_array($final)) {
            $final = $final['final'];
        }

        if ($separate) {
            return [
                'products_price' => $final,
                'shipper_price' => $this->shipper_price
            ];
        }

        return $final + $this->shipper_price;
    }

    public function setShipper()
    {
        $city = \CloudStore\App\Engine\Components\Request::getSession('checkout_region');

        $region = CloudStore::$app->store->loadOne("region", ["region_handle" => $city]);

        $ship_id = \CloudStore\App\Engine\Components\Request::post('checkout_ship_id');

        if (!$ship_id) {
            $ship_id = \CloudStore\App\Engine\Components\Request::getSession('shipper_id');
        }

        if (!$ship_id) {
            return false;
        }

        $shipper = CloudStore::$app->store->load("shipper", ["shipper_id" => $ship_id]);

        $full = $this->getShipper($shipper, $region);

        if (!$full['status']) {
            return false;
        }


        \CloudStore\App\Engine\Components\Request::setSession('shipper_name', $shipper[0]['shipper_type']);
        \CloudStore\App\Engine\Components\Request::setSession('shipper_price', $full['shippers'][0]['full_cost']);
        \CloudStore\App\Engine\Components\Request::setSession('shipper_id', $shipper[0]['shipper_id']);

        return [
            'shipper_type' => $shipper[0]['shipper_type'],
            'full_cost' => $full['shippers'][0]['full_cost'],
            'shipper_id' => $shipper[0]['shipper_id']
        ];
    }

    public function getShipper($shipper, $region)
    {
        if (!$shipper OR !$region) {
            return [
                'status' => false,
                'cause' => 'К сожалению, для вашего региона доставки нет'
            ];
        }

        $products = $this->getProducts();

        //Calculate full weight (products + packing)

        $full = 0;

        for ($i = 0, $c = count($products); $i < $c; $i++) {

            if (!$products[$i]['products_packing_weight'] OR $products[$i]['products_packing_weight'] == 0) {
                $packing = 1;
            } else {
                $packing = $products[$i]['products_packing_weight'];
            }

            $weight = $products[$i]['orders_count'] * ($products[$i]['products_weight'] ? $products[$i]['products_weight'] : 0);

            $packing_per_kg = CloudStore::$app->store->loadOne("settings", ["settings_name" => "delivery_packing_weight"])["settings_value"];

            if ($packing_per_kg == 0) {
                $packing_per_kg = 0.2;
            }

            $k = 1 / $packing_per_kg;
            $packing_weight = ceil($weight) / $k;

            $full += ($weight + $packing_weight);
        }

        for ($i = 0, $c = count($shipper); $i < $c; $i++) {

            $local_region = CloudStore::$app->store->loadOne("region_ship", ["region_shipper" => $shipper[$i]["shipper_id"], "region_id" => $region["region_id"]]);
            if ($local_region) {

                // Set region! Don't forget about saving default value.
                $_region = $region;
                $region = $local_region;

                $shipper[$i]["shipper_price"] = $region["region_shipper_price"];
                /* $shipper[$i]["shipper_duration"] = $region["region_shipper_duration"]; */
            }

            $weight_cost = 0;

            //If static
            if ($shipper[$i]['shipper_static'] === '1') {
                $shipper[$i]['full_cost'] = $shipper[$i]['shipper_price'];
                continue;
            }

            //If no values
            if (!$region['region_limit'] AND !$region['region_minimum'] AND !$region['region_maximum'] AND !$region['region_step'] AND !$region['region_increment'] AND !$region['region_minimum_cost'] AND !$region['region_overpay_cost']) {
                $shipper[$i]['full_cost'] = $shipper[$i]['shipper_price'];
                continue;
            }

            //If exceeding of limit
            if ($region['region_limit'] < $full) {

                unset($shipper[$i]);
                $region = $_region;
                continue;

            }


            //Regular
            if ($region['region_minimum'] !== NULL AND $region['region_minimum'] < $full) {
                $step = $region['region_step'];

                if ($step !== "0") {

                    if ($full > $region['region_maximum']) {
                        $full = $full - ($full - $region['region_maximum']);

                        $coef = ceil(($full - $region['region_minimum']) / $step);

                        $weight_cost = $region['region_increment'] * $coef;
                    } else {
                        $coef = ceil(($full - $region['region_minimum']) / $step);

                        $weight_cost = $region['region_increment'] * $coef;
                    }

                }
            }

            if ($region['region_overwrite_cost'] === '1') {

                $standart_cost = $region['region_minimum_cost'];
            } else {

                $standart_cost = $shipper[$i]['shipper_price'];
            }

            if ($region['region_overpay_check'] === '1') {

                $standart_cost += $region['region_overpay_cost'];
            }

            $full_cost = $standart_cost + $weight_cost;

            $shipper[$i]['full_cost'] = $full_cost;

            if (!empty($_region))
                $region = $_region;
        }

        if (!$shipper) {

            return [
                'status' => false,
                'cause' => 'К сожалению, для вашего региона доставки нет'
            ];
        } else {

            return [
                'status' => true,
                'shippers' => $shipper
            ];
        }

    }

    public function getProducts()
    {

        $ip = System::getUserIP();
        $sql = "SELECT o.orders_id, o.products_handle, o.orders_price, o.orders_count, o.orders_cart_id, o.products_modification, p.handle, p.title, p.image, p.category_id, p.price, p.quantity_available, p.id, p.quantity_stock, p.products_shipping_date, p.quantity_possible, p.products_possible_shipping_date, p.products_shipping_expectation, p.products_weight, p.products_packing_weight, c.name FROM order_products o "
            . "INNER JOIN products p ON o.id = p.id AND p.title <> '' AND o.store = p.store "
            . "LEFT JOIN category c ON p.category_id = c.category_id AND o.store = c.store "
            . "LEFT JOIN products_inventory pi ON o.products_modification = pi.id AND o.store = pi.store "
            . "WHERE orders_ip=:ip AND orders_status='0' AND o.store = :store";
        $array = CloudStore::$app->store->execGet($sql, [":ip" => $ip, ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]]);

        if (!$array) {
            return false;
        }

        for ($i = 0, $c = count($array); $i < $c; $i++) {

            if ($array[$i]["products_modification"] !== "0" && $modification = CloudStore::$app->store->loadOne("products_inventory", ["product" => $array[$i]["id"], "id" => $array[$i]["products_modification"]])) {

                $array[$i]['status'] = ModelCheckout::getStatus($modification['quantity_available'], $modification['quantity_stock'], $modification['products_shipping_date'], $modification['quantity_possible'], $modification['products_possible_shipping_date'], $array[$i]['products_shipping_expectation'], $array[$i]['id']);
                $array[$i]["title"] = $array[$i]["title"] . " [" . $modification["modification"] . "]";
                $array[$i]["price"] = $modification["price"];
            } else {

                $array[$i]['status'] = ModelCheckout::getStatus($array[$i]['quantity_available'], $array[$i]['quantity_stock'], $array[$i]['products_shipping_date'], $array[$i]['quantity_possible'], $array[$i]['products_possible_shipping_date'], $array[$i]['products_shipping_expectation'], $array[$i]['id']);
            }

            if (!$array[$i]['status']["shipping_avail"]) {
                CloudStore::$app->store->delete("cart", ["cart_id" => $array[$i]["orders_cart_id"]]);
                unset($array[$i]);
            }
        }

        return $array;
    }

    public function getProductsPrice($products, bool $setpoints = false)
    {
        $pre_price = null;
        if ($products) {
            foreach ($products as $cur) {
                $pre_price = $pre_price + $cur['orders_price'];
            }
        }


        if (\CloudStore\App\Engine\Components\Request::getSession('checkout_points_enabled') AND $setpoints === false) {
            $pre_price = ProductManager::usePoints($pre_price);
        }

        return $pre_price;
    }

    public function finishCheckout($price, $products)
    {
        $ip = System::getUserIP();

        $post = \CloudStore\App\Engine\Components\Request::post();

        //Calculate the price
        $full = $this->calculateFullPrice($products, true);

        $updated_price = $this->calculateCharge($post['checkout_payment_gateway'], $products, $full['shipper_price']);

        $products_new = $updated_price['products'] ?? NULL;
        $shipping_new = $updated_price['delivery'] ?? NULL;

        $payment_method = $this->paymentMethod($post['checkout_payment_gateway'], $full);

        $user_id = \CloudStore\App\Engine\Components\Request::getSession("user_id");

        //To session
        \CloudStore\App\Engine\Components\Request::setSession("shipper_price", $shipping_new);
        \CloudStore\App\Engine\Components\Request::setSession("full_price", $payment_method['payment_full_price']);

        // Constructions like \Site\Enigine... was added automatically by phpstorm!

        CloudStore::$app->store->collect("orders", [
            "orders_users_id" => \CloudStore\App\Engine\Components\Request::getSession("user_id"),
            "orders_price" => $payment_method['payment_full_price'],
            "orders_charge_price" => $payment_method['payment_full_charge'],
            "orders_shipping" => $this->shipper_name,
            "orders_shipping_price" => $this->shipper_price,
            "orders_name" => \CloudStore\App\Engine\Components\Request::getSession('checkout_name'),
            "orders_last_name" => \CloudStore\App\Engine\Components\Request::getSession('checkout_last_name'),
            "orders_company" => \CloudStore\App\Engine\Components\Request::getSession('checkout_company'),
            "orders_address" => \CloudStore\App\Engine\Components\Request::getSession('checkout_address'),
            "orders_region" => \CloudStore\App\Engine\Components\Request::getSession('checkout_region'),
            "orders_index" => \CloudStore\App\Engine\Components\Request::getSession('checkout_index'),
            "orders_city" => \CloudStore\App\Engine\Components\Request::getSession('checkout_city'),
            "orders_country" => \CloudStore\App\Engine\Components\Request::getSession('checkout_country'),
            "orders_flat" => \CloudStore\App\Engine\Components\Request::getSession('checkout_flat'),
            "orders_email" => \CloudStore\App\Engine\Components\Request::getSession('checkout_email'),
            "orders_phone" => \CloudStore\App\Engine\Components\Request::getSession('checkout_phone'),
            "orders_note" => \CloudStore\App\Engine\Components\Request::getSession('checkout_note'),
            "orders_billing_status" => \CloudStore\App\Engine\Components\Request::getSession('checkout_billing_address_payment'),
            "orders_billing_name" => \CloudStore\App\Engine\Components\Request::getSession('checkout_billing_first_name'),
            "orders_billing_last_name" => \CloudStore\App\Engine\Components\Request::getSession('checkout_billing_last_name'),
            "orders_billing_company" => \CloudStore\App\Engine\Components\Request::getSession('checkout_billing_company'),
            "orders_billing_address" => \CloudStore\App\Engine\Components\Request::getSession('checkout_billing_address'),
            "orders_billing_city" => \CloudStore\App\Engine\Components\Request::getSession('checkout_billing_city'),
            "orders_billing_country" => \CloudStore\App\Engine\Components\Request::getSession('checkout_billing_country'),
            "orders_billing_index" => \CloudStore\App\Engine\Components\Request::getSession('checkout_billing_index'),
            "orders_billing_phone" => \CloudStore\App\Engine\Components\Request::getSession('checkout_billing_phone'),
            "orders_payment" => $post['checkout_payment_gateway'],
            "orders_ip" => $ip,
            "orders_status" => 1,
            "orders_date" => CloudStore::$app->store->now()
        ]);

        $this->id = Database::getInstance()->lastInsertId();
        \CloudStore\App\Engine\Components\Request::setSession('last_order_id', $this->id);

        if (S::update("order_products", ["orders_final_id" => $this->id, "orders_status" => 1], ["orders_ip" => $ip, "orders_status" => 0])) {
            //Slow
            if (!empty($products_new)) {
                foreach ($products_new as $product) {

                    CloudStore::$app->store->update("order_products", ["orders_price" => $product['payment_full_price']], ["orders_id" => $product['orders_id'], "orders_ip" => $ip]);
                }
            }

            if (!empty($shipping_new)) {

                CloudStore::$app->store->update("orders", ["orders_shipping_price" => $shipping_new, "orders_default_shipping" => $full['shipper_price']], ["orders_id" => $this->id]);
            }

            // Add order key
            $this->key = sha1($this->id) . uniqid(20);


            // Send Email
            if (!$this->sendEmail($this->id, $this->key)) {
                return false;
            }

            $this->setCount($products);

            // Erase cart
            CloudStore::$app->store->delete("cart", ["cart_ip" => $ip]);

            CloudStore::$app->store->update("orders", ["orders_key" => $this->key], ["orders_id" => $this->id]);

            //CreatePDF
            $this->createPDF($this->id, $this->key);

            //Update Points
            $this->updatePoints($products);

            //Erase session
            \CloudStore\App\Engine\Components\Request::eraseFullSession("checkout");

            return $this->key;
        }

        return false;
    }

    public function calculateCharge($method, $products, $delivery)
    {
        $payment = CloudStore::$app->store->loadOne("payment", ["payment_id" => $method]);

        if ($payment['payment_charge_value'] === '0' OR !$payment['payment_charge_value']) {
            return $products;
        }

        foreach ($products as $key => $product) {
            $price = $product['orders_price'];

            if (strpos($payment['payment_charge_value'], "%")) {

                $charge = intval($payment['payment_charge_value']);

                $products[$key]['payment_full_price'] = $price + ($price / 100 * $charge);
            } else {

                $charge = intval($payment['payment_charge_value']);

                $products[$key]['payment_full_price'] = $price + $charge;
            }
        }

        //Delivery
        if ($payment['payment_charge_selector'] === '0') {

            $price = $delivery;

            if (strpos($payment['payment_charge_value'], "%")) {
                $charge = intval($payment['payment_charge_value']);

                $delivery = $price + ($price / 100 * $charge);
            } else {

                $charge = intval($payment['payment_charge_value']);

                $delivery = $price + $charge;
            }
        }


        return [
            'products' => $products,
            'delivery' => $delivery
        ];
    }

    public function paymentMethod($method, $full)
    {

        $payment = CloudStore::$app->store->loadOne("payment", ["payment_id" => $method]);

        if ($payment['payment_charge_selector'] === '0') {

            $price = $full['products_price'] + $full['shipper_price'];

            if (strpos($payment['payment_charge_value'], "%")) {
                $charge = intval($payment['payment_charge_value']);

                $payment['payment_full_price'] = $price + ($price / 100 * $charge);
                $payment['payment_full_charge'] = $price / 100 * $charge;
            } else {

                $charge = intval($payment['payment_charge_value']);

                $payment['payment_full_price'] = $price + $charge;
                $payment['payment_full_charge'] = $charge;
            }

            $payment['payment_full_charge_selector'] = 'no';
        } else {
            $price = $full['products_price'];

            if (strpos($payment['payment_charge_value'], "%")) {

                $charge = intval($payment['payment_charge_value']);

                $payment['payment_full_price'] = $price + ($price / 100 * $charge) + $full['shipper_price'];
                $payment['payment_full_charge'] = $price / 100 * $charge;
            } else {

                $charge = intval($payment['payment_charge_value']);

                $payment['payment_full_price'] = $price + $charge + $full['shipper_price'];
                $payment['payment_full_charge'] = $charge;
            }

            $payment['payment_full_charge_selector'] = 'yes';
        }

        return $payment;
    }

    public function sendEmail($id, $key)
    {

        try {

            $mailfrom = \CloudStore\App\Engine\Config\Config::$config['admin_email'];
            $mailto = \CloudStore\App\Engine\Components\Request::getSession('checkout_email');
            $subject = "Заказ #{$id} принят";

            $mailto_ad = \CloudStore\App\Engine\Config\Config::$config['admin_email'];
            $subject_ad = "[" . \CloudStore\App\Engine\Config\Config::$config['site_email_name'] . "] Заказ #{$id} " . \CloudStore\App\Engine\Components\Request::getSession('checkout_name') . ' ' . \CloudStore\App\Engine\Components\Request::getSession('checkout_last_name') . ' ' . \CloudStore\App\Engine\Components\Request::getSession('checkout_phone');

            $this->array = $this->getOrderProducts($id, true);

            $body = $this->prepareEmail($id, $key, $this->array);
            $body_ad = $this->prepareEmailAdmin($id, $key, $this->array);

            Utils::sendMail2($mailto, $mailfrom, $subject, $body, $this->array);

            Utils::sendMail2($mailto_ad, $mailfrom, $subject_ad, $body_ad, $this->array);

            $this->removeThumb($this->array);

            return true;
        } Catch (\Exception $e) {

            $this->removeThumb($this->array);

            System::exceptionToFile($e);
            return false;
        }
    }

    public function getOrderProducts($id, $thumb = false)
    {
        $ip = System::getUserIP();
        $sql = "SELECT o.products_handle, o.orders_price, o.orders_count, o.products_modification, p.handle, p.title, p.image, p.quantity_available, p.category_id, p.price, p.id, p.quantity_stock, p.products_shipping_date, p.quantity_possible, p.products_possible_shipping_date, p.products_shipping_expectation, c.name FROM order_products o "
            . "INNER JOIN products p ON o.id = p.id AND p.title <> '' AND o.store = p.store "
            . "LEFT JOIN category c ON p.category_id = c.category_id AND o.store = c.store "
            . "LEFT JOIN products_inventory pi ON o.products_modification = pi.id AND o.store = pi.store "
            . "WHERE orders_final_id=:id AND o.store = :store";
        $order_pr = CloudStore::$app->store->execGet($sql, [":id" => $id, ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]]);

        if (!$order_pr) {

            Router::errorPage404();
        }

        for ($i = 0, $c = count($order_pr); $i < $c; $i++) {
            if ($order_pr[$i]["products_modification"] !== "0" && $modification = CloudStore::$app->store->loadOne("products_inventory", ["product" => $order_pr[$i]["id"], "id" => $order_pr[$i]["products_modification"]])) {

                $order_pr[$i]['status'] = ModelCheckout::getStatus($modification['quantity_available'], $modification['quantity_stock'], $modification['products_shipping_date'], $modification['quantity_possible'], $modification['products_possible_shipping_date'], $order_pr[$i]['products_shipping_expectation'], $order_pr[$i]['id']);
                $order_pr[$i]["title"] = $order_pr[$i]["title"] . " [" . $modification["modification"] . "]";
                $order_pr[$i]["price"] = $modification["price"];
            } else {

                $order_pr[$i]['status'] = ModelCheckout::getStatus($order_pr[$i]['quantity_available'], $order_pr[$i]['quantity_stock'], $order_pr[$i]['products_shipping_date'], $order_pr[$i]['quantity_possible'], $order_pr[$i]['products_possible_shipping_date'], $order_pr[$i]['products_shipping_expectation'], $order_pr[$i]['id']);
            }
        }

        if (!$thumb) {
            return $order_pr;
        }

        $imagine = new \Imagine\Gd\Imagine;

        for ($i = 0; $i < count($order_pr); $i++) {

            if (!file_exists(THUMBNAILS . $order_pr[$i]['image']) OR is_dir(THUMBNAILS . $order_pr[$i]['image'])) {

                continue;
            }

            $current = $imagine->open(THUMBNAILS . $order_pr[$i]['image']);

            $size = $current->getSize();

            if ($size->getWidth() > $size->getHeight()) {
                $width = $size->getHeight();
                $height = $width;

                $orig = $size->getWidth();
                $offset = ($orig - $width) / 2;

                $new_size = new \Imagine\Image\Box($width, $height);
                $point = new \Imagine\Image\Point($offset, 0);
                $thumb = new \Imagine\Image\Box(60, 60);
            } else {
                $width = $size->getWidth();
                $height = $width;

                $orig = $size->getHeight();
                $offset = ($orig - $width) / 2;

                $new_size = new \Imagine\Image\Box($width, $height);
                $point = new \Imagine\Image\Point(0, $offset);
                $thumb = new \Imagine\Image\Box(60, 60);
            }

            $thumb_dir = THUMBNAILS . 'temp';
            if (!file_exists($thumb_dir)) {

                mkdir($thumb_dir);
            }

            $filename = $order_pr[$i]['image'];

            $ext = substr($filename, strrpos($filename, '.'));

            $thumb_name = $thumb_dir . '/temp_' . rand(0, 777) . $id . $i . $ext;

            $current->crop($point, $new_size)->thumbnail($thumb)->save($thumb_name);

            $order_pr[$i]['image'] = $thumb_name;
        }

        return $order_pr;
    }

    public function prepareEmail($id, $key, $array)
    {
        $session = \CloudStore\App\Engine\Components\Request::getSession();
        $tpl = file_get_contents(HOME . 'templates/mail/' . THEME_MAIL . 'ordermail_tpl.php');

        $tpl = str_replace("{{ORDERID}}", 'ORDER #' . $id, $tpl);
        $tpl = str_replace("{{ORDER_NAME}}", $session['checkout_name'], $tpl);
        $tpl = str_replace("{{ORDER_LAST_NAME}}", $session['checkout_last_name'], $tpl);
        $tpl = str_replace("{{ORDER_PHONE}}", $session['checkout_phone'], $tpl);
        $tpl = str_replace("{{ORDER_EMAIL}}", $session['checkout_email'], $tpl);

        $items = $this->prepareEmailItems($array);

        $tpl = str_replace("{{HOST}}", Router::getHost(), $tpl);
        $tpl = str_replace("{{SITE_NAME}}", \CloudStore\App\Engine\Config\Config::$config['site_name'], $tpl);
        $tpl = str_replace("{{ADMIN_EMAIL}}", \CloudStore\App\Engine\Config\Config::$config['admin_email'], $tpl);

        $tpl = str_replace("{{ITEMS}}", $items, $tpl);
        $tpl = str_replace("{{SHIPPER_NAME}}", $session['shipper_name'] . ' - ' . $this->shipper_price, $tpl);
        $tpl = str_replace("{{SHIPPER_PRICE}}", Utils::asPrice($this->shipper_price), $tpl);
        $tpl = str_replace("{{FULL_PRICE}}", Utils::asPrice($session['full_price']), $tpl);
        $tpl = str_replace("{{CHECKOUT_ADDRESS}}", $session['checkout_address'], $tpl);
        $tpl = str_replace("{{CHECKOUT_CITY}}", $session['checkout_city'], $tpl);
        $tpl = str_replace("{{CHECKOUT_REGION}}", $session['checkout_region'], $tpl);
        $tpl = str_replace("{{CHECKOUT_INDEX}}", $session['checkout_index'], $tpl);
        $tpl = str_replace("{{CHECKOUT_COUNTRY}}", $session['checkout_country'], $tpl);
        $tpl = str_replace("{{COMPANY_NAME}}", $session['checkout_company'], $tpl);

        if ($session['checkout_billing_address_payment'] === '0') {

            $tpl = str_replace("{{CHECKOUT_BILLING_NAME}}", $session['checkout_name'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_LAST_NAME}}", $session['checkout_last_name'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_PHONE}}", $session['checkout_phone'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_ADDRESS}}", $session['checkout_address'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_CITY}}", $session['checkout_city'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_REGION}}", $session['checkout_region'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_INDEX}}", $session['checkout_index'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_COUNTRY}}", $session['checkout_country'], $tpl);
            $tpl = str_replace("{{COMPANY_BILLING_NAME}}", $session['checkout_company'], $tpl);
        } else {


            $tpl = str_replace("{{CHECKOUT_BILLING_NAME}}", $session['checkout_billing_first_name'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_LAST_NAME}}", $session['checkout_billing_last_name'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_PHONE}}", $session['checkout_billing_phone'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_ADDRESS}}", $session['checkout_billing_address'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_CITY}}", $session['checkout_billing_city'], $tpl);
//        $tpl = str_replace("{{CHECKOUT_BILLING_REGION}}", $session['checkout_billing_region'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_INDEX}}", $session['checkout_billing_index'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_COUNTRY}}", $session['checkout_billing_country'], $tpl);
            $tpl = str_replace("{{COMPANY_BILLING_NAME}}", $session['checkout_billing_company'], $tpl);
        }

        $link = Router::getHost() . "/checkout/thank_you?orderid={$key}";
        $tpl = str_replace("{{ORDER_LINK}}", $link, $tpl);

        return $tpl;
    }

    public function prepareEmailItems($array)
    {
        $body = null;
        if ($array) {
            foreach ($array as $cur) {

                $body .= '<tr class="m_-1216999618458073902order-list__item m_-1216999618458073902order-list__item" style="width:100%">'
                    . '<td class="m_-1216999618458073902order-list__item__cell" style="font-family:-apple-system,BlinkMacSystemFont,\'' . 'Segoe UI' . '\',\'' . 'Roboto' . '\',\'' . 'Oxygen' . '\',\'' . 'Ubuntu' . '\',\'' . 'Cantarell' . '\',\'' . 'Fira Sans' . '\',\'' . 'Droid Sans' . '\',\'' . 'Helvetica Neue' . '\',sans-serif">
                    <table style="border-collapse:collapse;border-spacing:0">
                      <tbody><tr>
                      <td style="font-family:-apple-system,BlinkMacSystemFont,\'' . 'Roboto' . '\',\'' . 'Oxygen' . '\',\'' . 'Ubuntu' . '\',\'' . 'Cantarell' . '\',\'' . 'Fira Sans' . '\',\'' . 'Droid Sans' . '\',\'' . 'Helvetica Neue' . '\',sans-serif;">
                          <img src="cid:' . $cur['handle'] . '" align="left" width="60px" height="60px" class="m_-1216999618458073902order-list__product-image CToWUd" style="border:1px solid #e5e5e5;border-radius:8px;margin-right:15px;">
                      </td>
                      <td class="m_-1216999618458073902order-list__product-description-cell" style="font-family:-apple-system,BlinkMacSystemFont,\'' . 'Roboto' . '\',\'' . 'Oxygen' . '\',\'' . 'Ubuntu' . '\',\'' . 'Cantarell' . '\',\'' . 'Fira Sans' . '\',\'' . 'Droid Sans' . '\',\'' . 'Helvetica Neue' . '\',sans-serif;width:75%">

                        <span class="m_-1216999618458073902order-list__item-title" style="color:#555;font-size:16px;font-weight:600;line-height:1.4">' . $cur['title'] . '&nbsp;×&nbsp;' . $cur['orders_count'] . '</span><br>
                        <span class="m_-1216999618458073902order-list__item-title" style="color:#555;font-size:13px;font-weight:300;line-height:1.4">Возможность доставки и самовывоза: <span style="font-weight:600">' . $cur['status']['shipping_date'] . '</span></span><br>

                      </td>
                        <td class="m_-1216999618458073902order-list__price-cell" style="font-family:-apple-system,BlinkMacSystemFont,\'' . 'Roboto' . '\',\'' . 'Oxygen' . '\',\'' . 'Ubuntu' . '\',\'' . 'Cantarell' . '\',\'' . 'Fira Sans' . '\',\'' . 'Droid Sans' . '\',\'' . 'Helvetica Neue' . '\',sans-serif;white-space:nowrap">

                          <p class="m_-1216999618458073902order-list__item-price" style="color:#555;font-size:16px;font-weight:600;line-height:150%;margin:0 0 0 15px" align="right">' . Utils::asPrice($cur['orders_price']) . '</p>
                        </td>
                    </tr></tbody></table>
                  </td></tr>';
            }

            return $body;
        }

        return false;
    }

    public function prepareEmailAdmin($id, $key, $array)
    {
        $session = \CloudStore\App\Engine\Components\Request::getSession();
        $tpl = file_get_contents(HOME . 'templates/mail/' . THEME_MAIL . 'ordermail_tpl.php');

        $tpl = str_replace("{{ORDERID}}", 'ORDER #' . $id, $tpl);
        $tpl = str_replace("{{ORDER_NAME}}", $session['checkout_name'], $tpl);
        $tpl = str_replace("{{ORDER_LAST_NAME}}", $session['checkout_last_name'], $tpl);
        $tpl = str_replace("{{ORDER_PHONE}}", $session['checkout_phone'], $tpl);
        $tpl = str_replace("{{ORDER_EMAIL}}", $session['checkout_email'], $tpl);

        $items = $this->prepareEmailItems($array);

        $tpl = str_replace("{{HOST}}", Router::getHost(), $tpl);
        $tpl = str_replace("{{SITE_NAME}}", \CloudStore\App\Engine\Config\Config::$config['site_name'], $tpl);
        $tpl = str_replace("{{ADMIN_EMAIL}}", \CloudStore\App\Engine\Config\Config::$config['admin_email'], $tpl);

        $tpl = str_replace("{{ITEMS}}", $items, $tpl);
        $tpl = str_replace("{{SHIPPER_NAME}}", $session['shipper_name'] . ' - ' . $this->shipper_price, $tpl);
        $tpl = str_replace("{{SHIPPER_PRICE}}", Utils::asPrice($this->shipper_price), $tpl);
        $tpl = str_replace("{{FULL_PRICE}}", Utils::asPrice($session['full_price']), $tpl);
        $tpl = str_replace("{{CHECKOUT_ADDRESS}}", $session['checkout_address'], $tpl);
        $tpl = str_replace("{{CHECKOUT_CITY}}", $session['checkout_city'], $tpl);
        $tpl = str_replace("{{CHECKOUT_REGION}}", $session['checkout_region'], $tpl);
        $tpl = str_replace("{{CHECKOUT_INDEX}}", $session['checkout_index'], $tpl);
        $tpl = str_replace("{{CHECKOUT_COUNTRY}}", $session['checkout_country'], $tpl);
        $tpl = str_replace("{{COMPANY_NAME}}", $session['checkout_company'], $tpl);

        if ($session['checkout_billing_address_payment'] === '0') {

            $tpl = str_replace("{{CHECKOUT_BILLING_NAME}}", $session['checkout_name'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_LAST_NAME}}", $session['checkout_last_name'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_PHONE}}", $session['checkout_phone'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_ADDRESS}}", $session['checkout_address'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_CITY}}", $session['checkout_city'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_REGION}}", $session['checkout_region'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_INDEX}}", $session['checkout_index'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_COUNTRY}}", $session['checkout_country'], $tpl);
            $tpl = str_replace("{{COMPANY_BILLING_NAME}}", $session['checkout_company'], $tpl);
        } else {


            $tpl = str_replace("{{CHECKOUT_BILLING_NAME}}", $session['checkout_billing_first_name'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_LAST_NAME}}", $session['checkout_billing_last_name'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_PHONE}}", $session['checkout_billing_phone'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_ADDRESS}}", $session['checkout_billing_address'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_CITY}}", $session['checkout_billing_city'], $tpl);
//        $tpl = str_replace("{{CHECKOUT_BILLING_REGION}}", $session['checkout_billing_region'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_INDEX}}", $session['checkout_billing_index'], $tpl);
            $tpl = str_replace("{{CHECKOUT_BILLING_COUNTRY}}", $session['checkout_billing_country'], $tpl);
            $tpl = str_replace("{{COMPANY_BILLING_NAME}}", $session['checkout_billing_company'], $tpl);
        }

        $link = Router::getHost() . "/checkout/thank_you?orderid={$key}";
        $tpl = str_replace("{{ORDER_LINK}}", $link, $tpl);

        return $tpl;
    }

    public function removeThumb($array)
    {
        foreach ($array as $current) {
            @unlink($current['image']);
        }
    }

    public function setCount($products)
    {
        if (!$products) {
            return false;
        }

        $db = Database::getInstance();

        foreach ($products as $product) {

            if ($product["products_modification"] !== "0" && $modification = CloudStore::$app->store->loadOne("products_inventory", ["product" => $product["id"], "id" => $product["products_modification"]])) {

                CloudStore::$app->store->execGet("UPDATE products_inventory SET quantity_available = quantity_available - :count WHERE product = :product AND id = :id AND store = :store", [
                    ":count" => $product['orders_count'],
                    ":product" => $product["id"],
                    ":id" => $modification["id"],
                    ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]
                ]);
            } else {

                CloudStore::$app->store->execSet("UPDATE products SET quantity_available = quantity_available - :count WHERE id = :handle AND store = :store", [
                    ":count" => $product['orders_count'],
                    ":handle" => $product['id'],
                    ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]
                ]);
            }
        }

        return true;
    }

    public function createPDF($id, $key)
    {
        //CreatePDF
        @Utils::createPDF();

        //Create Link
        $db = Database::getInstance();
        $lnk = \CloudStore\App\Engine\Config\Config::$config['orders_location'] . "order{$id}.pdf";

        if (!S::collect("orders_documents", ["orders_id" => $id, "document_file" => $lnk, "orders_key" => $key])) {
            return false;
        }

        return true;
    }

    public function updatePoints($products)
    {
        //Update points
        if (\CloudStore\App\Engine\Components\Request::getSession('checkout_points_enabled')) {

            $points = $this->getProductsPrice($products)['points'];
            $points = (int)$points;
            $id = \CloudStore\App\Engine\Components\Request::getSession('user_id');

            if (!S::update("users", ["users_points" => $points], ["users_id" => $id])) {
                return false;
            }
        }

        return true;
    }

    public function validateStep1()
    {

        $post = \CloudStore\App\Engine\Components\Request::post();

        if (!$post) {
            return true;
        }

        // First name
        if (!$post['checkout_name']) {
            $this->errors['name'] = [
                'class' => 'field--error',
                'message' => '<p class="field__message field__message--error" id="error-for-last_name">Пожалуйста, введите Ваше имя</p>'
            ];
        }

        // Last name
        if (!$post['checkout_last_name']) {
            $this->errors['last_name'] = [
                'class' => 'field--error',
                'message' => '<p class="field__message field__message--error" id="error-for-last_name">Пожалуйста, введите Вашу фамилию</p>'
            ];
        }

        // Email
        if (!$post['checkout_email'] OR !preg_match("/^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i", trim($post['checkout_email']))) {
            $this->errors['email'] = [
                'class' => 'field--error',
                'message' => '<p class="field__message field__message--error" id="error-for-email">Пожалуйста, введите адрес действующей электронной почты</p>'
            ];
        }

        // Address
        if (!$post['checkout_address']) {
            $this->errors['address'] = [
                'class' => 'field--error',
                'message' => '<p class="field__message field__message--error" id="error-for-address1">Пожалуйста, введите адрес</p>'
            ];
        }

        // City
        if (!$post['checkout_city']) {
            $this->errors['city'] = [
                'class' => 'field--error',
                'message' => '<p class="field__message field__message--error" id="error-for-city">Пожалуйста, введите Ваш город</p>'
            ];
        }

        // Region
        if (!isset($post['checkout_region'])) {
            $this->errors['region'] = [
                'class' => 'field--error',
                'message' => '<p class="field__message field__message--error" id="error-for-city">Пожалуйста, введите Ваш регион</p>'
            ];
        }

        // Index
        if (!$post['checkout_index']) {
            $this->errors['index'] = [
                'class' => 'field--error',
                'message' => '<p class="field__message field__message--error" id="error-for-zip">Пожалуйста, введите Ваш почтовый индекс</p>'
            ];
        }

        // Phone
        if (!$post['checkout_phone']) {
            $this->errors['phone'] = [
                'class' => 'field--error',
                'message' => '<p class="field__message field__message--error" id="error-for-phone">Пожалуйста, введите действующий номер телефона</p>'
            ];
        }

        // other
        //...
        //final
        if (count($this->errors) > 0) {
            return $this->errors;
        } else {
            return false;
        }
    }

    public function validateStep3()
    {
        $post = \CloudStore\App\Engine\Components\Request::post();

        if (!$post) {
            return true;
        }
        if ($post['checkout_billing_address_payment'] === '0') {
            return false;
        }

        //Billing
        // Last name
        if (!$post['checkout_billing_last_name']) {
            $this->errors['billing_last_name'] = [
                'class' => 'field--error',
                'message' => '<p class="field__message field__message--error" id="error-for-last_name">Пожалуйста, введите Вашу фамилию</p>'
            ];
        }

        // Address
        if (!$post['checkout_billing_address']) {
            $this->errors['billing_address'] = [
                'class' => 'field--error',
                'message' => '<p class="field__message field__message--error" id="error-for-last_name">Пожалуйста, введите адрес</p>'
            ];
        }

        // City
        if (!$post['checkout_billing_city']) {
            $this->errors['billing_city'] = [
                'class' => 'field--error',
                'message' => '<p class="field__message field__message--error" id="error-for-last_name">Пожалуйста, введите Ваш город</p>'
            ];
        }

        // Index
        if (!$post['checkout_billing_index']) {
            $this->errors['billing_index'] = [
                'class' => 'field--error',
                'message' => '<p class="field__message field__message--error" id="error-for-last_name">Пожалуйста, введите Ваш индекс</p>'
            ];
        }

        // Phone
        if (!$post['checkout_billing_phone']) {
            $this->errors['billing_phone'] = [
                'class' => 'field--error',
                'message' => '<p class="field__message field__message--error" id="error-for-last_name">Пожалуйста, введите Ваш телефон</p>'
            ];
        }

        //final
        if (count($this->errors) > 0) {
            return $this->errors;
        } else {
            return false;
        }
    }
}
