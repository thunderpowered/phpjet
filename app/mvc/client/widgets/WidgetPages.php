<?php

namespace CloudStore\App\MVC\Client\Widgets;

use CloudStore\CloudStore;
use CloudStore\App\Engine\Config\Config;
use CloudStore\App\Engine\Core\Widget;

/**
 * Class WidgetPages
 * @package CloudStore\App\MVC\Client\Widgets
 */
class WidgetPages extends Widget
{
    /**
     * @var array
     */
    private $pages;

    /**
     * @param array $IDs
     * @return string
     */
    public function getWidget(array $IDs = array()): string
    {
        $this->pages = $this->getPages($IDs);
        return $this->render("widget_pages", [
            "pages" => $this->pages
        ]);
    }

    /**
     * @param array $IDs
     * @return array
     */
    public function getPages(array $IDs = array()): array
    {
        if ($IDs) {
            $placeholders = implode(',', array_fill(0, count($IDs), '?'));
            $IDs[] = Config::$config["site_id"];
            $this->pages = CloudStore::$app->store->execGet("SELECT * FROM pages WHERE pages_id IN ({$placeholders}) AND store = ?", $IDs, false);
        } else {
            $this->pages = CloudStore::$app->store->load("pages", ["pages_main" => 1], array(), array(), false);
        }

        return $this->pages;
    }
}
