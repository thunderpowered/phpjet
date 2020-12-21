<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 2018-06-21
 * Time: 7:51
 */

namespace CloudStore\App\MVC\Client\Models;

use CloudStore\App\Engine\Core\Model;
use CloudStore\App\Engine\Core\System;

class ModelRobots extends Model
{
    private $disallow = [

        "/search/",
        "/catalog/all/",
        "/cart/",
        "/checkout/"
    ];

    private $text = "";

    public function robots()
    {

        $this->text .= $this->disallow();
        $this->text .= $this->sitemap();
        $this->downloadRobots();
    }

    private function disallow() : string
    {

        if (!$this->disallow) {

            return "";
        }

        $text = "";
        foreach ($this->disallow as $item) {

            $text .= "Disallow: " . $item . "\n";
        }

        return $text;
    }

    private function sitemap()
    {

        return "sitemap: " . Router::getHost() . "/sitemap/index.xml";
    }

    private function downloadRobots()
    {

        $this->text = "User-agent: *\n" . $this->text;
        header("Content-Type: text/plain");

        echo $this->text;
        exit();
    }
}