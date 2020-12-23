<?php

namespace CloudStore\App\Engine\Ajax\Handlers;

use CloudStore\App\Engine\Components\Filter;
use CloudStore\App\Engine\Components\Request;
use CloudStore\App\Engine\Core\System;

class AjaxCategory
{

    public function filter_prepare()
    {
        $csrf = Request::get('csrf');

        if (!Utils::validateToken($csrf)) {
            return false;
        }

        $string = Router::getRoute(false)[3];

        $result = Filter::getFromString($string, 999);

        $count = $result ? count($result) : 0;

        return json_encode([
            "status" => true,
            "count" => $count
        ]);
    }
}
