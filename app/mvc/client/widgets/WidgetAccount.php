<?php

namespace CloudStore\App\MVC\Client\Widgets;

use CloudStore\App\Engine\Core\Widget;

/**
 * Class WidgetAccount
 * @package CloudStore\App\MVC\Client\Widgets
 */
class WidgetAccount extends Widget
{
    /**
     * @return string
     */
    public function getWidget(): string
    {
        return $this->render("widget_account", []);
    }
}
