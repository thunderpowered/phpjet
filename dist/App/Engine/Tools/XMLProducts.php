<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Jet\App\Engine\Tools;

use Jet\App\Engine\Config\Config;
use Jet\App\Engine\Core\Component;
use Jet\App\Engine\Core\Router;
use Jet\App\Engine\Core\Store;

class XMLProducts extends Component
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
    private static $mapName = "map.xml";
    private static $mapPrefix = "google";

    public static function getXML(string $sitemap)
    {
        XMLProducts::checkMap();
        XMLProducts::download($sitemap);
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

            // @todo use inventory check for modifications

            if ($count > 0) {

                $pickup = 'in stock';
            } elseif ($count <= 0 AND $count > (0 - $cur['quantity_stock'])) {

                $pickup = 'in stock';
            } elseif ($count <= 0 AND $count < (0 - $cur['quantity_stock']) AND $cur['products_possible_shipping_date'] AND ($count > (0 - ($cur['quantity_stock'] + $cur['quantity_possible'])))) {

                $pickup = 'in stock';
            } else {

                $pickup = 'out of stock';
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

            $cur['description'] = preg_replace("|<h\d>(.+)</h\d>|isU", '', $cur['description']);
            $cur['description'] = strip_tags($cur['description']);
            $cur['description'] = str_replace("&nbsp;", "", $cur['description']);
            $cur['description'] = str_replace("{{SPECIFICATIONS}}", "", $cur['description']);
            $cur['description'] = strip_tags($cur["description"]);
            $cur['description'] = html_entity_decode($cur["description"]);
            $cur['description'] = trim($cur["description"]);
            $cur['description'] = preg_replace('/[\r\n]+/s', "\n", $cur["description"]);

            $cur['description'] = Utils::removeSpecialChars($cur['description']);

            if (empty(str_replace(" ", "", $cur['description']))) {

                $cur['description'] = "Описание отсутствует";
            }

            if (mb_strlen($cur["title"]) >= 150) {

                $cur["title"] = mb_substr($cur["title"], 0, 147) . "...";
            }

            $offer .= '<item>
                            <g:id>' . $cur['id'] . '</g:id>
                            <g:title>' . preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', html_entity_decode($cur['title'])) . '</g:title>
                            <g:description>' . $cur['description'] . '</g:description>
                            <g:link>' . Config::$config['curl_address'] . 'products/' . $cur['handle'] . '</g:link>
                            <g:image_link>' . Config::$config['curl_address'] . "uploads/images/" . $cur['image'] . '</g:image_link>
                            <g:availability>' . $pickup . '</g:availability>
                            <g:condition>new</g:condition>
                            <g:price>' . Utils::asPrice($cur['price']) . '</g:price>
                            <g:brand>' . preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $cur['brand']) . '</g:brand>
                        </item>
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
            $deliveries .= '<option cost="' . $cur['shipper_price'] . '" days="' . $cur['shipper_duration'] . '"/>
                            ';
        }

        return $deliveries;
    }

    private static function getCategories()
    {
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

    private static function generateXML()
    {

        XMLProducts::$date = date('Y-m-d H:s:m');
        XMLProducts::$name = Config::$config['site_name'];
        XMLProducts::$comp_name = Config::$config['site_name'];
        XMLProducts::$comp_url = Config::$config['curl_address'];
        XMLProducts::$categories = XMLProducts::getCategories();
        XMLProducts::$deliveries = XMLProducts::getDeliveries();
        XMLProducts::$offers = XMLProducts::getOffers();


        XMLProducts::$body = '<?xml version="1.0" encoding="UTF-8"?>
                        <rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
                        <channel>
                        <title>' . XMLProducts::$name . '</title>
                        <link>' . XMLProducts::$comp_url . '</link>
                        <description>Товары ' . XMLProducts::$name . '</description>
                        ' . XMLProducts::$offers . '
                        </channel>
                        </rss>';

        $name = XMLProducts::$mapPrefix . "-" . XMLProducts::$mapName;
        return XMLProducts::publishMap($name, XMLProducts::$body);
    }

    private static function updateSettings()
    {

        $current = PHPJet::$app->store->loadOne("settings", ["settings_name" => "google_xml"]);

        if (!$current) {

            Store::collect("settings", ["settings_name" => "google_xml", "settings_value" => Store::now()]);
        } else {

            Store::update("settings", ["settings_value" => Store::now()], ["settings_name" => "google_xml"]);
        }
    }

    private static function publishMap(string $mapName, string $map): bool
    {

        $siteID = Config::$config["site_id"];

        if (!file_exists(XMLProducts::$directory)) {

            mkdir(XMLProducts::$directory);
        }

        $siteDIR = XMLProducts::$directory . $siteID . "/";

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
        $current = PHPJet::$app->store->loadOne("settings", ["settings_name" => "google_xml"]);

        // Temp
        // !$current
        if (true) {

            // It's not correct way of using XMLMap class
            // Need to get products/categories/etc right in model
            // And then generate XML in XMLMap
            XMLProducts::generateXML();
            XMLProducts::updateSettings();

            return true;
        }

        $datetime = \DateTime::createFromFormat("Y-m-d H:i:s", $current["settings_value"]);
        if (!$datetime) {

            XMLProducts::generateXML();
            XMLProducts::updateSettings();
        } else {

            $currentTime = new \DateTime();
            $diff = $currentTime->diff($datetime);
            $days = +$diff->format("%d");

            if ($diff && $days > 1) {

                XMLProducts::generateXML();
                XMLProducts::updateSettings();
            }
        }

        return true;
    }

    private static function download($sitemap)
    {

        $filename = XMLProducts::$directory . Config::$config["site_id"] . "/" . Config::$config["site_id"] . "-" . XMLProducts::$mapPrefix . "-" . $sitemap;
        if (!file_exists($filename)) {

            Router::errorPage404();
        }

        header("Content-Type: text/xml");
        readfile($filename);
        exit();
    }
}
