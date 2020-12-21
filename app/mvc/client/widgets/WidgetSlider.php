<?php

namespace CloudStore\App\MVC\Client\Widgets;

use CloudStore\App\Engine\Core\Widget;
use CloudStore\CloudStore;

/**
 * Class WidgetSlider
 * @package CloudStore\App\MVC\Client\Widgets
 */
class WidgetSlider Extends Widget
{
    /**
     * @var array
     */
    public $slider = null;

    /**
     * @return bool
     */
    public function getWidget(): string
    {
        if (!$this->getSlider()) {
            return false;
        }

        $slider_settings = CloudStore::$app->store->loadOne("settings", ["settings_name" => "slider_mode"]);
        if ($slider_settings AND $slider_settings["settings_value"] === "classic") {
            $slider_view = "widget_slider_classic_2";
        } else {
            $slider_view = "widget_slider_tiles";
        }

        return $this->render($slider_view, [
            "slider" => $this->slider
        ]);
    }

    /**
     * @return array
     */
    public function getSlider()
    {
        if (!$this->slider) {
            $this->slider = CloudStore::$app->store->load("slider", [], [], [], false);
        }

        return $this->slider;
    }
}
