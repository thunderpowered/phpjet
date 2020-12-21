<?php

namespace CloudStore\App\Engine\Ajax\Handlers;

use CloudStore\App\Engine\Components\Business;

class AjaxCheckout
{

    public function use_points()
    {
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            return false;
        }

        $ip = \CloudStore\App\Engine\Core\System::getUserIP();

        $csrf = \CloudStore\App\Engine\Components\Utils::removeSpecialChars($_POST['csrf']);

        if (!\CloudStore\App\Engine\Components\Utils::validateToken($csrf)) {
            return false;
        }

        \CloudStore\App\Engine\Components\Request::setSession('checkout_points_enabled', true);

        $products = $this->getProducts($ip);

        $price = $this->getProductsPrice($products, true);

        $price['delta_int'] = $price['delta'];
        $price['final'] = \CloudStore\App\Engine\Components\Utils::asPrice($price['final']);
        $price['delta'] = ($price['delta'] == 0 ? '' : '-') . \CloudStore\App\Engine\Components\Utils::asPrice($price['delta']);

        return json_encode($price, JSON_UNESCAPED_UNICODE);
    }

    public function getProducts($ip)
    {

        $ip = \CloudStore\App\Engine\Core\System::getUserIP();
        $sql = "SELECT o.products_handle, o.orders_price, o.orders_count, p.handle, p.title, p.image, p.category_id, p.price, c.name FROM order_products o "
            . "RIGHT JOIN products p ON o.products_handle = p.handle AND p.title <> '' AND o.store = p.store "
            . "RIGHT JOIN category c ON p.category_id = c.category_id AND o.store = c.store "
            . "WHERE orders_ip=:ip AND orders_status='0' AND o.store = :store";
        $array = \CloudStore\App\Engine\Components\S::execGet($sql, [":ip" => $ip, ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]]);
        if (!$array) {
            return false;
        }

        return $array;
    }

    public function getProductsPrice($products, $setpoints = false)
    {

        $pre_price = null;
        if ($products) {
            foreach ($products as $cur) {
                $pre_price = $pre_price + $cur['orders_price'];
            }
        }
        if (\CloudStore\App\Engine\Components\Request::getSession('checkout_points_enabled') AND $setpoints === true) {
            $pre_price = ProductManager::usePoints($pre_price);
        }

        return $pre_price;
    }

    public function disable_points()
    {
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            return false;
        }

        $ip = \CloudStore\App\Engine\Core\System::getUserIP();

        $csrf = \CloudStore\App\Engine\Components\Request::post('csrf');

        if (!\CloudStore\App\Engine\Components\Utils::validateToken($csrf)) {
            return false;
        }

        \CloudStore\App\Engine\Components\Request::setSession('checkout_points_enabled', false);

        $products = $this->getProducts($ip);

        $price = $this->getProductsPrice($products, false);

        return \CloudStore\App\Engine\Components\Utils::asPrice($price);

        //$price['final'] = ShopEngine::Help()->as_price($price['final']);
        //return json_encode($price, JSON_UNESCAPED_UNICODE);
    }

    public function select_region()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $id = \CloudStore\App\Engine\Components\Request::post('id');
            $csrf = \CloudStore\App\Engine\Components\Request::post('csrf');

            if (!\CloudStore\App\Engine\Components\Utils::validateToken($csrf)) {
                return false;
            }

            $sql = "SELECT * FROM region WHERE country_id IN ("
                . "SELECT country_id FROM countries WHERE country_handle = :handle) AND region_avail = '1' AND store = :store";

            $region = \CloudStore\App\Engine\Components\S::execGet($sql, [":handle" => $id, ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]]);

            if (!$region) {
                return 500;
            }

            echo '<option selected value="" disabled="">Регион</option>';

            foreach ($region as $reg) {
                echo '<option value="' . $reg["region_handle"] . '" >' . $reg["region_name"] . '</option>';
            }
        }

        return 500;
    }
}
