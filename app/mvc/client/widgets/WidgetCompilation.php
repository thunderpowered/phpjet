<?php

namespace CloudStore\App\MVC\Client\Widgets;

use CloudStore\App\Engine\Components\Filter;
use CloudStore\App\Engine\Core\Widget;

class WidgetCompilation extends Widget
{

    public function getCompilation($string)
    {

        $products = Filter::getFromString($string, 8);

        return $this->render("widget_compilation", [
            "products" => $products
        ]);
    }
}
