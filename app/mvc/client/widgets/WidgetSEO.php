<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 2018-07-23
 * Time: 12:53
 */

namespace CloudStore\App\MVC\Client\Widgets;

use CloudStore\CloudStore;
use CloudStore\App\Engine\Core\Widget;

class WidgetSEO extends Widget
{

    private $codeFields = ["google_analytics", "google_searchconsole", "yandex_webmaster", "yandex_metrika"];

    public function getAnalytics()
    {

        $codes = $this->getCodes();

        $this->render("widget_seo_analytics", ["codes" => $codes, "codeFields" => $this->codeFields]);
    }

    private function getCodes()
    {

        $codes = [];

        foreach ($this->codeFields as $name) {

            // Code or false
            $codes[$name] = CloudStore::$app->store->loadOne("settings", ["settings_name" => $name]);
            if ($codes[$name]) {

                $codes[$name] = $codes[$name]["settings_value"];
            }
        }

        return $codes;
    }
}