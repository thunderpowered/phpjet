<?php

namespace CloudStore\App\Engine\Ajax\Handlers;

use CloudStore\App\Engine\Components\Request;
use CloudStore\App\Engine\Components\S;
use CloudStore\App\Engine\Config\Config;
use CloudStore\App\Engine\Core\System;

class AjaxSystem
{

    public $title;
    public $brand;

    public function price_template()
    {
        if ($_SERVER["REQUEST_METHOD"] != "POST") {

            return false;
        }

        //$ip = ShopEngine::getUserIp();
//        $csrf  = \CloudStore\App\Engine\Components\Request::Post('csrf');
//        
//        if(!ShopEngine::Help()->validate_token($csrf))
//        {
//            return false;
//        }

        $price_template = \CloudStore\App\Engine\Components\S::loadOne("settings", ["settings_name" => "price_template"], false);

        $price_template = unserialize($price_template['settings_value']);

        return json_encode($price_template);
    }

    public function ajax_search()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Token
            $csrf = Request::post('csrf');
            $str = Request::post('value');
            if (!$str) {

                return $this->response(false, $str);
            }
            $array = explode(' ', $str);

            $slike = "%" . $str . "%";

            //First step
            $sql = "SELECT * FROM products WHERE (title LIKE ? OR brand LIKE ? OR products_sku LIKE ? OR id = ?) AND avail='1' AND price <> '0.00' AND store = ? LIMIT 10 ";
            //$result = Product\CloudStore\App\Engine\Components\S::loadExec($sql, ['%'.$str.'%', '%'.$str.'%', \CloudStore\App\Engine\Config\Config::$config["site_id"]], false);
            $result = CloudStore::$app->store->execGet($sql, [$slike, $slike, $slike, $str, Config::$config["site_id"]], false);

            if ($result) {

                return $this->response($result, $str);
            }

            //Second step
            $place = [];

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

            $place[] = Config::$config["site_id"];

            $sql = "SELECT * FROM products WHERE ( {$this->title} OR {$this->brand} ) AND avail='1' AND price <> '0.00' AND title <> '' AND store = ? LIMIT 10 ";
            $result = CloudStore::$app->store->execGet($sql, $place, false);

            if ($result) {

                return $this->response($result, $str);
            }

            //Last step
            $this->title = null;

            $place = [];

            foreach ($array as $key => $value) {
                if (isset($array[$key - 1])) {
                    $this->title .= " OR ";
                }
                $this->title .= "(title LIKE ? AND avail='1' AND price <> 0.00 AND title <> '') OR (brand LIKE ? AND avail='1' AND price <> 0.00 AND title <> '')";
                $place[] = "%$value%";
                $place[] = "%$value%";
            }

            $place[] = Config::$config["site_id"];

            $sql = "SELECT * FROM products WHERE ( {$this->title} ) AND avail='1' AND price <> '0.00' AND store = ? LIMIT 10 ";

            $result = CloudStore::$app->store->execGet($sql, $place, false);

            if ($result) {

                return $this->response($result, $str);
            }
        }

        return $this->response(false, $str);
    }

    public function response($result, $str)
    {
        if ($result AND count($result) > 0) {
            $count = count($result);
            for ($i = 0; $i < (count($result) < 10 ? count($result) : 10); $i++) {
                echo
                    '<li class="javascript_no_hide search_results_li">'
                    . '<a href="' . \CloudStore\App\Engine\Core\Router::getHost() . '/products/' . $result[$i]['handle'] . '">'
                    . '<span class="thumbnail">' . \CloudStore\App\Engine\Components\Utils::getThumbnail($result[$i]['image']) . '</span>'
                    . '<span class="title"><strong>' . $result[$i]['brand'] . ' ' . $result[$i]['title'] . '</strong></span>'
                    . '<span class="price">' . $result[$i]['price'] . '</span>'
                    . '</a>'
                    . '</li>';
            }

            echo
                '<li class="javascript_no_hide">'
                . '<span class="title">'
                . '<a href="/search/?q=' . trim($str) . '">See all results (' . $count . ')</a>'
                . '</span>'
                . '</li>';
            return false;
        }

        echo '500';
        return false;
    }
}
