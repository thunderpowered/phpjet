<?php

namespace CloudStore\App\MVC\Client\Widgets;

use CloudStore\App\Engine\Core\Store;
use CloudStore\CloudStore;

class WidgetInfo extends \CloudStore\App\Engine\Core\Widget
{

    public $label_1;
    public $label_2;

    private $favicon;
    private $logotype;

    public function __construct()
    {
        parent::__construct();
        // TEMPORARY IN CONSTRUCT

        $favicon = CloudStore::$app->store->loadOne("settings", ["settings_name" => "site_favicon"]);
        $logotype = CloudStore::$app->store->loadOne("settings", ["settings_name" => "site_logotype"]);

        if ($favicon) {

            $this->favicon = CloudStore::$app->tool->utils->getImageLink($favicon["settings_value"]);
        } else {

            $this->favicon = THEME_STATIC_URL . "img/favicon.ico";
        }

        if ($logotype) {

            $this->logotype = CloudStore::$app->tool->utils->getImageLink($logotype["settings_value"]);
        }

        $label_1 = CloudStore::$app->store->loadOne("settings", ["settings_name" => "information_header_label_1"]);
        $label_2 = CloudStore::$app->store->loadOne("settings", ["settings_name" => "information_header_label_2"]);

        $this->label_1 = $label_1["settings_value"] ?? null;
        $this->label_2 = $label_2["settings_value"] ?? null;
    }

    public function getFavicon()
    {
        return $this->favicon;
    }

    public function getLogotype()
    {
        return $this->logotype;
    }

    public function getAmountOfProducts()
    {
        //@todo store it in database
        return CloudStore::$app->store->count("products");
    }

}
