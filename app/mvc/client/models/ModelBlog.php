<?php

namespace CloudStore\App\MVC\Client\Models;

use CloudStore\App\Engine\Components\Getter;
use CloudStore\App\Engine\Components\S;
use CloudStore\App\Engine\Config\Config;
use CloudStore\App\Engine\Core\Model;

class ModelBlog extends Model
{

    /**
     * @param $post
     * @return array|bool|string
     */
    public function getTags($post)
    {

        return CloudStore::$app->store->execGet("SELECT * FROM taxonomy_items ti LEFT JOIN taxonomy t ON ti.taxonomy = t.id AND ti.store = t.store WHERE item = :item AND ti.store = :store", [":item" => $post["id"], ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]]);

    }

    public function getPosts($category = null)
    {

        if ($category) {

            $sql = "SELECT * FROM blog b LEFT JOIN taxonomy t ON b.category = t.id WHERE b.store = ? AND t.store = ? AND t.type = 'b_cat' AND b.category = ? ORDER BY b.date DESC, b.id DESC";
            return Getter::getDataWithPagination($sql, [Config::$config["site_id"], Config::$config["site_id"], $category]);
        } else {

            $sql = "SELECT * FROM blog b LEFT JOIN taxonomy t ON b.category = t.id  WHERE b.store = ? AND t.store = ? AND t.type = 'b_cat' ORDER BY b.date DESC, b.id DESC";
            return Getter::getDataWithPagination($sql, [Config::$config["site_id"], Config::$config["site_id"]]);
        }

    }

    public function getPostsByTag($tag = null)
    {
        $sql = "SELECT * FROM taxonomy_items ti LEFT JOIN blog b ON ti.item = b.id AND ti.store = b.store WHERE ti.taxonomy = ? AND ti.store = ? ORDER BY date DESC, b.id DESC";
        return Getter::getDataWithPagination($sql, [$tag, Config::$config["site_id"]]);
    }

}