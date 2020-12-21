<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CloudStore\App\Engine\Tools;

use CloudStore\App\Engine\Config\Config;
use CloudStore\App\Engine\Core\Component;
use CloudStore\App\Engine\Core\Router;
use CloudStore\App\Engine\Core\Store;
use CloudStore\App\Engine\Core\System;

class YMLMap extends Component
{

    private static $body;
    private static $date;
    private static $name;
    private static $comp_name;
    private static $comp_url;
    private static $categories;
    private static $deliveries;
    private static $offers;

    private static $directory = HOME . "files/sitemaps/";
    private static $mapName = "map.yml";
    private static $mapPrefix = "yandex";

    public static function getYML(string $sitemap)
    {
        YMLMap::checkMap();
        YMLMap::download($sitemap);
    }

    private static function getOffers()
    {

        $sql = "SELECT * FROM products p LEFT JOIN category c ON p.category_id = c.category_id AND p.store = c.store WHERE p.category_id <> 0 AND avail='1' AND price <> 0.00 AND p.store = ?";
        $array = Store::execGet($sql, [Config::$config["site_id"]], false);

        //$array = Getter::getFreeData($sql, [Config::$config["site_id"], Config::$config["site_id"]]);
        if (!$array) {

            return false;
        }

        $offer = null;


        foreach ($array as $cur) {

            $count = $cur['quantity_available'];


            if ($count > 0) {

                $pickup = 'true';
            } elseif ($count <= 0 AND $count > (0 - $cur['quantity_stock'])) {

                $pickup = 'true';
            } elseif ($count <= 0 AND $count < (0 - $cur['quantity_stock']) AND $cur['products_possible_shipping_date'] AND ( $count > (0 - ($cur['quantity_stock'] + $cur['quantity_possible'])))) {

                $pickup = 'true';
            } else {

                continue;
                $pickup = 'false';
            }


            if ((strtotime($cur['products_shipping_date']) - time()) < 0 AND $cur['quantity_available'] > 0) {

                $duration = '1-3';
            } elseif ((strtotime($cur['products_shipping_date']) - time()) < 0 AND $cur['quantity_available'] <= 0) {

                $duration = '0';
            } elseif ((strtotime($cur['products_shipping_date']) - time()) > 0 AND $cur['quantity_available'] <= 0) {

                $date_1 = new \DateTime("now");
                $date_2 = new \DateTime($cur['products_shipping_date']);

                $diff = $date_2->diff($date_1);
                $shipping_diff = $diff->format("%d");

                $duration = $shipping_diff . '-' . ((int)$shipping_diff + 2);
            } else {
                $duration = '1-3';
            }

            // Prepare description
            // @todo handle it
            $cur['description'] = preg_replace("|<h\d>(.+)</h\d>|isU", '', $cur['description']);
            $cur['description'] = str_replace("&nbsp;", "", $cur['description']);
            $cur['description'] = str_replace("{{SPECIFICATIONS}}", "", $cur['description']);
            $cur['description'] = strip_tags($cur["description"]);
            $cur['description'] = html_entity_decode($cur["description"]);
            $cur['description'] = trim($cur["description"]);
            $cur['description'] = preg_replace('/[\r\n]+/s',"\n",
                preg_replace('/[\r\n][ \t]+/s',"\n", $cur['description']));

            if (empty(str_replace(" ", "", $cur["description"]))) {

                $cur['description'] = "Описание отсутствует";
            }


            $offer .= '<offer available="' . $pickup . '" type="vendor.model" id="' . $cur['id'] . '">
                            <price>' . (int) $cur['price'] . '</price>
                            <url>' . Config::$config['curl_address'] . 'products/' . $cur['handle'] . '</url>
                            <currencyId>RUR</currencyId>
                            <pickup>true</pickup>
                            <delivery>true</delivery>
                            <picture>' . Config::$config['curl_address'] . "uploads/images/" . $cur['image'] . '</picture>
                            <typePrefix>' . $cur['name'] . '</typePrefix>
                            <vendor>' . preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;',$cur['brand']) . '</vendor>
                            <model>' . preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;',$cur['title']) . '</model>
                            <categoryId>' . $cur['category_id'] . '</categoryId>
                            <description>
                                <![CDATA[ ' . $cur["description"] . ' ]]>
                            </description>
                            <vendorCode/>
                            <sales_notes></sales_notes>
                            <delivery-options>
                                <option cost="400" days="' . $duration . '"/>
                            </delivery-options>
                        </offer>
                        ';
        }

        return $offer;
    }

    private static function getDeliveries()
    {

        $array = Store::load("shipper", ["shipper_duration" => "!0"]);

        if (!$array) {
            return false;
        }

        $deliveries = null;

        foreach ($array as $cur) {
//            $firstSymbol = (int)$cur['shipper_duration'];
//            if ($firstSymbol > 3) {
//                continue;
//            }
            $deliveries .= '<option cost="' . $cur['shipper_price'] . '" days="' . $cur['shipper_duration'] . '"/>
                            ';
        }

        return $deliveries;
    }

    private static function getCategories()
    {

        //$sql = "SELECT category_id, name FROM category";
        //$array = Getter::getFreeData($sql,null,false);

        $array = Store::load("category");

        if (!$array) {
            return false;
        }

        $categories = null;

        foreach ($array as $cur) {
            $categories .= '<category id="' . $cur['category_id'] . '">' . Utils::replaceASCII($cur['name']) . '</category>
                            ';
        }

        return $categories;
    }

    private static function generateYML()
    {

        YMLMap::$date = date('Y-m-d H:s:m');
        YMLMap::$name = Config::$config['site_name'];
        YMLMap::$comp_name = Config::$config["site_name"];
        YMLMap::$comp_url = Config::$config['curl_address'];
        YMLMap::$categories = YMLMap::getCategories();
        YMLMap::$deliveries = YMLMap::getDeliveries();
        YMLMap::$offers = YMLMap::getOffers();


        YMLMap::$body = '<?xml version="1.0" encoding="UTF-8"?>
                        <yml_catalog date="' . YMLMap::$date . '">
                        <shop>
                        <name>' . YMLMap::$name . '</name>
                        <company>' . YMLMap::$comp_name . '</company>
                        <url>' . YMLMap::$comp_url . '</url>
                        <currencies>
                        <currency id="RUR" rate="1"/>
                        </currencies>
                        <categories>
                            ' . YMLMap::$categories . '
                        </categories>
                        <delivery-options> 
                            ' . YMLMap::$deliveries . '
                        </delivery-options>
                        <cpa>1</cpa>
                        <offers>         
                            ' . YMLMap::$offers . '
                        </offers>
                        </shop>
                        </yml_catalog>';

        $name = YMLMap::$mapPrefix . "-" . YMLMap::$mapName;
        return YMLMap::publishMap($name, YMLMap::$body);
    }

    private static function updateSettings()
    {

        $current = CloudStore::$app->store->loadOne("settings", ["settings_name" => "yandex_yml"]);

        if (!$current) {

            Store::collect("settings", ["settings_name" => "yandex_yml", "settings_value" => Store::now()]);
        } else {

            Store::update("settings", ["settings_value" => Store::now()], ["settings_name" => "yandex_yml"]);
        }
    }

    private static function publishMap(string $mapName, string $map): bool
    {

        $siteID = Config::$config["site_id"];

        if (!file_exists(YMLMap::$directory)) {

            mkdir(YMLMap::$directory);
        }

        $siteDIR = YMLMap::$directory . $siteID . "/";

        if (!file_exists($siteDIR)) {

            mkdir($siteDIR);
        }

        $filename = $siteDIR . $siteID . "-" . $mapName;

        try {

            $file = fopen($filename, 'w');
            fwrite($file, $map);
            fclose($file);
        } catch (\Exception $e) {

            // If something went wrong, sitemap just won't be showed
            // There're nothing we can do
            return false;
        }

        return true;
    }

    private static function checkMap()
    {

        // Check datetime of last generation
        $current = CloudStore::$app->store->loadOne("settings", ["settings_name" => "yandex_yml"]);

        // Temp
        // !$current
        if (true) {

            // It's not correct way of using XMLMap class
            // Need to get products/categories/etc right in model
            // And then generate XML in XMLMap
            YMLMap::generateYML();
            YMLMap::updateSettings();

            return true;
        }

        $datetime = \DateTime::createFromFormat("Y-m-d H:i:s", $current["settings_value"]);
        if (!$datetime) {

            YMLMap::generateYML();
            YMLMap::updateSettings();
        } else {

            $currentTime = new \DateTime();
            $diff = $currentTime->diff($datetime);
            $days = +$diff->format("%d");

            if ($diff && $days > 1) {

                YMLMap::generateYML();
                YMLMap::updateSettings();
            }
        }

        return true;
    }

    private static function download($sitemap)
    {

        $filename = YMLMap::$directory . Config::$config["site_id"] . "/" . Config::$config["site_id"] . "-" . YMLMap::$mapPrefix . "-" . $sitemap;
        if (!file_exists($filename)) {

            Router::errorPage404();
        }

        header("Content-Type: text/xml");
        readfile($filename);
        exit();
    }

}
