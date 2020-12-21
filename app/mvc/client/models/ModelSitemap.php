<?php

namespace CloudStore\App\MVC\Client\Models;


use CloudStore\App\Engine\Components\S;
use CloudStore\App\Engine\Core\Model;
use CloudStore\App\Engine\Core\System;

class ModelSitemap extends Model
{

    private $host;
    public function __construct($name = null)
    {
        Parent::__construct();
        $this->host = Router::getHost();
    }

    public function showSitemap($sitemap)
    {

        // There was something
    }

    public function getSiteMap()
    {
        $sitemap = [];
        $sitemap["site"] = $this->buildHTML($this->getSiteMapSite());

        $sitemap["categories"] = $this->getSiteMapCategories();
        return $sitemap;
    }

    private function getSiteMapCategories() : array
    {
        $categories = CloudStore::$app->store->loadOne("settings", ["settings_name" => "menu"], false);
        if (!$categories) {
            return [];
        }

        $categories = json_decode($categories["settings_value"]);
        $categories = $this->buildCategories($categories);

        $categoriesLeft = $this->buildHTML($categories["left"]);
        $categoriesRight = $this->buildHTML($categories["right"]);

        return [
            "left" => $categoriesLeft,
            "right" => $categoriesRight
        ];
    }

    private function getSiteMapSite()
    {
        // Pages
        $pages = $this->getPages();

        // Other
        $other = [];
        $other["children"]["Корзина"] = [
            "url" => $this->host . "/cart"
        ];

        return [
            "Сайт" => [
                "children" => [
                    "Информация" => $pages,
                    "Другое" => $other
                ]
            ]
        ];
    }

    private function buildCategories(array $array = [], bool $isSub = false) : array
    {
        if (!$array) {
            return [];
        }

        $categories = [];
        foreach ($array as $key => $item) {

            $children = $this->buildCategories($item->children, true);

            $categories[$item->title] = [
                "url" => "/catalog/" . ($item->url ?? $item->category_handle),
                "children" => $children
            ];
        }

        if (!$isSub) {
            $categories = $this->distributeCategories($categories);
        }

        return $categories;
    }

    private function distributeCategories(array $categories = []) : array
    {
        if (!$categories) {
            return [
                "left" => [],
                "right" => []
            ];
        }

        $i = 0;
        $categoriesDistributed = [
            "left" => [],
            "right" => []
        ];

        foreach ($categories as $title => $category) {
            if ($i % 2 === 0) {
                $categoriesDistributed["left"][$title] = $category;
            } else {
                $categoriesDistributed["right"][$title] = $category;
            }
            $i++;
        }

        return $categoriesDistributed;
    }

    private function buildHTML(array $array = [], bool $isSub = false) : string
    {

        if (!$array) {
            return "";
        }

        $subClass = $isSub ? "" : "tree";
        $subList = $isSub ? "" : "map-list__item--top";

        $string = "";
        $string .= "<ul class='map-list {$subClass}'>";
        foreach ($array as $key => $value) {
            $string .= "<li class='map-list__item {$subList}'>";
            if (!empty($value["children"])) {
                if (!empty($value["url"])) {
                    $string .= "<a class='map-list__link' href='{$value["url"]}'>{$key}</a>";
                } else {
                    $string .= "<a class='map-list__link'>{$key}</a>";
                }
                $string .= $this->buildHTML($value["children"], true);
            } else if (empty($value["children"]) && !empty($value["url"])) {
                $string .= "<a class='map-list__link' href='{$value["url"]}'>{$key}</a>";
            } else {
                $string .= "<a class='map-list__link' href='{$value['url']}'>{$key}</a>";
            }
            $string .= "</li>";
        }
        $string .= "</ul>";
        return $string;
    }

    private function getPages()
    {
        $pages = CloudStore::$app->store->load("pages");
        $pagesPrepared = [];
        if ($pages) {
            foreach ($pages as $key => $page) {
                $title = $page["pages_title"];
                $url = $this->host . "/pages/" . $page["pages_handle"];

                $pagesPrepared["children"][$title] = [
                    "url" => $url
                ];
            }
        }

        return $pagesPrepared;
    }
}