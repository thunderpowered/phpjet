<?php

namespace CloudStore\App\Engine\Controllers;

use CloudStore\App\Engine\Components\Request;
use CloudStore\App\Engine\Core\Store;
use CloudStore\App\Engine\Config\Config;
use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\Engine\Core\System;
use CloudStore\App\Engine\Components\Utils;

class ControllerCheckout extends Controller
{

    public static $shippers;

    private static $data;
    private static $order_pr;
    private static $price = null;
    private static $pre_price = null;
    private $errors = null;

    // @todo move all help methods to model
    // Only actions in the controller!
    public static function getCountry()
    {
        $country = Request::getSession('checkout_country');

        $result = Store::load("countries", ["country_handle" => $country]);

        return $result["country_name"] ?? $country;
    }

    public static function getRegion()
    {
        $region = Request::getSession('checkout_region');

        $result = Store::load("region", ["region_name" => $region]);

        return $result["region_name"] ?? $region;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function type()
    {
        return 'act';
    }

    public function actionStep1()
    {

        /*
         * 
         *    First step
         * 1. Getting information about products in cart;
         * 2. Getting information from user (address, phone etc.);
         * 3. Entering all information into session;
         */

        $token = Request::get('token');
        if (!Utils::validateCheckoutToken($token)) {
            return Router::hoHome();
        }

        $this->title = "Оформление заказа. Шаг 1";

        $this->view->setLayout("checkout");
//        $this->view->layout = "checkout";

        $ip = System::getUserIP();

        if (Request::post('checkout_step1')) {

            $csrf = Request::post('csrf');
            if (!Utils::validateToken($csrf)) {
                return false;
            }

            $post = Request::post();
            Request::postToSession($post);

            $this->errors = Controller::getModel()->validateStep1();

            if (!$this->errors) {

                Request::setSession('checkout_complete_step1', true);
                return Utils::strongRedirect('checkout', 'step2?token=' . $token);
            }
        }

        //If user is logged we have to get all contact information about him
        if (Request::getSession('user_is_logged')) {
            $id = Request::getSession('user_id');
            $sql = "SELECT * FROM user_addresses a "
                . "INNER JOIN countries co ON a.address_country = co.country_handle AND a.store = co.store "
                . "INNER JOIN region r ON a.address_region = r.region_handle AND a.store = r.store "
                . "WHERE address_user=:id AND a.store = :store";
            $addresses = Store::execGet($sql, [":id" => $id, ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]]);
        } else {
            $addresses = null;
        }

        $products = $this->model->getProducts();

        if (!$products) {
            return Router::hoHome();
        }
        $price = $this->model->getProductsPrice($products, true);

        $regions = null;

        if ($country_handle = Request::getSession('checkout_country')) {
            $sql = "SELECT * FROM countries c "
                . "RIGHT JOIN region r ON c.country_id = r.country_id AND c.store = r.store "
                . "WHERE c.country_handle = :handle AND r.region_avail = '1' AND c.store = :store";

            $regions = Store::execGet($sql, [":handle" => $country_handle, ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]]);
        }

        $countries = Store::load("countries", ["country_avail" => 1]);

        return $this->view->render("view_checkout_step1", [
            'error' => $this->errors,
            'addresses' => $addresses,
            'order_products' => $products,
            'order_price' => $price,
            'regions' => $regions,
            'countries' => $countries,
            'token' => $token
        ]);
    }

    public function actionStep2()
    {
        /*
         * Second step
         * 1. Get information about delivery method in selected place;
         * 2. Show this information to user;
         * 3. Get new information from user;
         * 4. Recalculate price;
         */

        $token = Request::get('token');
        if (!Utils::validateCheckoutToken($token)) {
            return Router::hoHome();
        }

        $this->title = "Оформление заказа. Шаг 2";

        $this->view->setLayout("checkout");

        $ip = System::getUserIP();

        if (!$products = $this->model->getProducts($ip)) {
            return Router::hoHome();
        }

        // Full Price   
        $full = $this->model->getProductsPrice($products, true);

        if (Request::post('checkout_step2')) {

            $csrf = Request::post('csrf');
            if (!Utils::validateToken($csrf)) {
                return false;
            }

            $post = Request::post();
            Request::postToSession($post);

            // Shipping price and type

            if (!$this->model->setShipper()) {
                return false;
            }

            // Set into session
            Request::setSession('full_price', $full + Request::getSession('shipper_price'));
            //

            Request::setSession('checkout_complete_step2', true);
            return Utils::strongRedirect('checkout', 'step3?token=' . $token);
        }

        if (!Request::getSession('checkout_complete_step1')) {
            return Utils::strongRedirect('checkout', 'step1');
        }
        $city = Request::getSession('checkout_region');

        $region = CloudStore::$app->store->loadOne("region", ["region_handle" => $city]);

        $sql = "SELECT r.region_shipper, s.shipper_id, r.region_name, s.shipper_type, s.shipper_price, s.shipper_static FROM region_ship r RIGHT JOIN shipper s ON r.region_shipper = s.shipper_id AND r.store = s.store WHERE region_id=:handle AND r.store = :store";

        $shipping = Store::execGet($sql, [":handle" => $region["region_id"], ":store" => Config::$config["site_id"]]);

        $shipping = $this->model->getShipper($shipping, $region);

        return $this->view->render("view_checkout_step2", [
            'shipping' => $shipping,
            'checkout_products' => $products,
            'checkout_price' => $full,
            'token' => $token
        ]);
    }

    public function actionStep3()
    {
        /*
         * 
         * Pre final step
         * 1. Show information about payment methods;
         * 2. Get method from user;
         * 3. If user wants to use his poits to pay, recalculate full price and amount of points;
         */

        $token = Request::get('token');
        if (!Utils::validateCheckoutToken($token)) {
            return Router::hoHome();
        }

        $this->title = "Оформление заказа. Шаг 3";

        $this->view->setLayout("checkout");

        $ip = System::getUserIP();

        if (!$products = $this->model->getProducts($ip)) {
            return Router::hoHome();
        }

        if (!Request::getSession('checkout_complete_step2')) {
            return Utils::strongRedirect('checkout', 'step2');
        }

        $pre = $this->model->getProductsPrice($products, true);
        $price = $this->model->getProductsPrice($products, false);
        if (is_array($price)) {
            $price = $price['final'];
        }

        $shipp = Request::getSession('shipper_price');
        $full_price = $price + $shipp;
        Request::setSession('full_price', $full_price);

        $errors = null;

        if (Request::post('checkout_step3')) {

            $csrf = Request::post('csrf');
            if (!Utils::validateToken($csrf)) {
                return false;
            }

            $post = Request::post();
            Request::postToSession($post);

            $errors = $this->model->validateStep3();
            if (!$errors) {
                $price = $this->model->getProductsPrice($products);
                if ($key = $this->model->finishCheckout($price, $products)) {
                    //Mailer returns strings
                    return Utils::strongRedirect('checkout', 'thank_you?orderid=' . $key);
                } else {
                    $post = Request::post();
                    return Utils::strongRedirect('errorpage', '');
                }
            }
        }

        $payment_methods = $this->model->getPayment($products);

        $countries = Store::load("countries", ["country_avail" => 1]);

        return $this->view->render("view_checkout_step3", [
            'payment_methods' => $payment_methods,
            'full_price' => $full_price,
            'shipper_price' => $shipp,
            'checkout_price' => $pre,
            'checkout_products' => $products,
            'error' => $errors,
            'token' => $token,
            'countries' => $countries
        ]);
    }

    public function actionThank_you()
    {
        /*
         * 
         * Final step
         * 1. Show full information about purchase;
         * 2. Generate link to this page;
         * 3. Generate bill for payment;
         */

        $this->view->setLayout("checkout");
        $key = Request::get('orderid');
        if (!$key) {
            return Utils::regularRedirect('checkout', 'step3');
        }

        $array = Store::execGet("SELECT * FROM orders o LEFT JOIN payment p ON o.orders_payment = p.payment_id AND o.store = p.store WHERE orders_key=:key AND orders_status='1' AND o.store = :store", [":key" => $key, ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]])[0];

        if (count($array) < 1) {
            return Utils::strongRedirect('checkout', 'step3');
        }
        $products = $this->model->getOrderProducts($array['orders_id']);

        $final = $this->model->getProductsPrice($products);

        $this->title = "Заказ №{$array['orders_id']}";

        return $this->view->render("view_checkout_final", [
            'info' => $array,
            'checkout_products' => $products,
            'final' => $final
        ]);
    }

    public function actionDownload()
    {
        $this->view->setLayout("checkout");
        $key = Request::get('orderid');
        if (!$key) {
            return Utils::regularRedirect('checkout', 'thank_you');
        }

        if (!$array = CloudStore::$app->store->loadOne("orders_documents", ["orders_key" => $key])) {
            return Utils::regularRedirect('checkout', 'thank_you');
        }

        return Utils::forcedDownload(HOME . $array['document_file']);
    }
}
