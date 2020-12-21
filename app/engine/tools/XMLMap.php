<?php

namespace CloudStore\App\Engine\Tools;

use CloudStore\App\Engine\CloudStore;
use CloudStore\App\Engine\Config\Config;

/**
 * Class XMLMap
 * @package CloudStore\App\Engine\Tools
 */
class XMLMap
{
    /**
     * @var string
     */
    private static $host;
    /**
     * @var bool
     */
    private static $products;
    /**
     * @var bool
     */
    private static $categories;
    /**
     * @var bool
     */
    private static $general;
    /**
     * @var string
     */
    private static $directory;

    /**
     * @param string $sitemap
     * @return bool
     */
    public static function showMap(string $sitemap): bool
    {

        self::$directory = HOME . "/files/sitemaps/";

        try {
            self::checkMap();
            self::download($sitemap);
            return true;
        } catch (\Exception $e) {
//            CloudStore::$app->exit('XML map generation failed');
            return false;
        }
    }

    /**
     * @param string $directory
     */
    private static function generate(string $directory)
    {
        self::$host = CloudStore::$app->router->getHost();

        // Generating products
        self::$products = self::generateProducts();

        // Generating categories
        self::$categories = self::generateCategories();

        // Generating pages
        self::$general = self::generateGeneral();

        // Creating index XML map
        self::generateIndex();
    }

    /**
     * @return bool
     */
    private static function generateProducts(): bool
    {
        $products = CloudStore::$app->store->load("products", ["avail" => 1, "price" => "!0"]);

        if (!$products) {

            return false;
        }

        $xmlProducts = "";
        foreach ($products as $product) {

            $xmlProducts .= self::setItem(self::$host . "/products/" . $product["handle"], $product["datetime"]);
        }

        // Publish
        $xmlPages = self::setMap($xmlProducts);
        self::publishMap("products.xml", $xmlPages);

        return true;
    }

    /**
     * @return bool
     */
    private static function generateCategories(): bool
    {
        $categories = CloudStore::$app->store->load("category");

        if (!$categories) {
            return false;
        }

        $xmlCategories = "";
        foreach ($categories as $category) {
            $xmlCategories .= self::setItem(self::$host . "/catalog/" . $category["category_handle"], $category["datetime"]);
        }

        // Publish
        $xmlPages = self::setMap($xmlCategories);
        self::publishMap("catalog.xml", $xmlPages);

        return true;
    }

    /**
     * @return bool
     */
    private static function generateGeneral(): bool
    {
        $xmlGeneral = self::setItem(self::$host . "/");

        $pages = CloudStore::$app->store->load("pages");
        if ($pages) {

            foreach ($pages as $page) {

                $xmlGeneral .= self::setItem(self::$host . "/pages/" . $page["pages_handle"], $page["datetime"]);
            }
        }

        $blog = CloudStore::$app->store->load("blog");
        if ($blog) {

            $xmlGeneral .= self::setItem(self::$host . "/blog/");

            foreach ($blog as $_blog) {

                $xmlGeneral .= self::setItem(self::$host . "/blog/" . $_blog["url"], $_blog["datetime"]);
            }
        }
        // Publish
        $xmlGeneral = self::setMap($xmlGeneral);
        self::publishMap("general.xml", $xmlGeneral);

        return true;
    }

    /**
     * @param string $url
     * @param null $time
     * @return string
     */
    private static function setItem(string $url, $time = null): string
    {
        if (!$url) {
            return "";
        }


        // Open the tag
        $tempURL = "<url>";

        //Loc
        $tempURL .= "<loc>" . $url . "</loc>";

        // Getting date
        if ($time) {
            $modTime = \DateTime::createFromFormat("Y-m-d H:i:s", $time);
            $modTime = $modTime->format($modTime::W3C);

            $tempURL .= "<lastmod>" . $modTime . "</lastmod>";
        }

        $tempURL .= "</url>";

        return $tempURL;

    }

    /**
     * @param string $items
     * @return string
     */
    private static function setMap(string $items): string
    {
        if (!$items) {
            return "";
        }

        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">" . $items . "</urlset>";
    }

    private static function generateIndex()
    {
        $tempIndex = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";

        // Set products
        if (self::$products) {

            $tempIndex .= "<sitemap><loc>" . self::$host . "/sitemap/products.xml" . "</loc></sitemap>";
        }

        // Set categories
        if (self::$categories) {

            $tempIndex .= "<sitemap><loc>" . self::$host . "/sitemap/catalog.xml" . "</loc></sitemap>";
        }

        // Set pages
        if (self::$general) {

            $tempIndex .= "<sitemap><loc>" . self::$host . "/sitemap/general.xml" . "</loc></sitemap>";
        }

        $tempIndex .= "</sitemapindex>";

        self::publishMap("index.xml", $tempIndex);
    }

    /**
     * @param string $mapName
     * @param string $map
     * @return bool
     */
    private static function publishMap(string $mapName, string $map): bool
    {
        $siteID = Config::$config["site_id"];

        if (!file_exists(self::$directory)) {

            mkdir(self::$directory);
        }

        $siteDIR = self::$directory . $siteID . "/";

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

    /**
     * @param string $siteMap
     */
    private static function download(string $siteMap)
    {
        $filename = self::$directory . Config::$config["site_id"] . "/" . Config::$config["site_id"] . "-" . $siteMap;
        if (!file_exists($filename)) {

            CloudStore::$app->router->errorPage404();
        }

        header("Content-Type: text/xml");
        readfile($filename);
        exit();
    }

    private static function updateSettings()
    {
        $current = CloudStore::$app->store->loadOne("settings", ["settings_name" => "sitemap"]);

        if (!$current) {
            CloudStore::$app->store->collect("settings", ["settings_name" => "sitemap", "settings_value" => CloudStore::$app->store->now()]);
        } else {
            CloudStore::$app->store->update("settings", ["settings_value" => CloudStore::$app->store->now()], ["settings_name" => "sitemap"]);
        }
    }

    /**
     * @return bool
     * @throws \Exception
     */
    private static function checkMap(): bool
    {
        // Check datetime of last generation
        $current = CloudStore::$app->store->loadOne("settings", ["settings_name" => "sitemap"]);
        if (!$current) {

            // It's not correct way of using XMLMap class
            // Need to get products/categories/etc right in model
            // And then generate XML in XMLMap
            self::generate(self::$directory);
            self::updateSettings();

            return true;
        }

        $datetime = \DateTime::createFromFormat("Y-m-d H:i:s", $current["settings_value"]);
        if (!$datetime) {

            self::generate(self::$directory);
            self::updateSettings();
        } else {

            $currentTime = new \DateTime();
            $diff = $currentTime->diff($datetime);
            $days = +$diff->format("%d");

            if ($diff && $days > 1) {

                self::generate(self::$directory);
                self::updateSettings();
            }
        }

        return true;
    }
}