<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 23.09.2018
 * Time: 18:42
 */

namespace CloudStore\App\Engine\Ajax\Handlers;

use CloudStore\App\Engine\Components\S;

class AjaxLoad
{

    public function menu()
    {

        return false;

        $menu = CloudStore::$app->store->loadOne("settings", ["settings_name" => "menu"]);
        if (!$menu) {

            return json_encode(["status" => "false", "cause" => "Menu is not created. Go to admin panel, then create the menu."]);
        }

        $menu = $menu["settings_value"];
        $menu = ["status" => true, "menu" => $menu];

        return json_encode($menu);
    }

}