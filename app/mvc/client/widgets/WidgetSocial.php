<?php

namespace CloudStore\App\MVC\Client\Widgets;

use CloudStore\App\Engine\Core\Widget;

class WidgetSocial extends Widget
{
    /**
     * @return string
     * @deprecated
     */
    public function getWidget(): string
    {

        $social = \CloudStore\App\Engine\Components\S::load("settings", ["settings_section" => "social-links"]);

        return $this->render("widget_social", [
            "social" => $social
        ]);
    }
}
