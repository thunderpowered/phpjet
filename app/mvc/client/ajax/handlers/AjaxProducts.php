<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 2018-09-26
 * Time: 18:57
 */

namespace CloudStore\App\Engine\Ajax\Handlers;


use CloudStore\App\Engine\Components\ProductManager;
use CloudStore\App\Engine\Components\Request;
use CloudStore\App\Engine\Components\S;
use CloudStore\App\Engine\Core\System;

class AjaxProducts
{

    private $false;
    private $true;

    public function __construct()
    {
        $this->false = json_encode([
           "status" => false
        ]);
        $this->true = json_encode([
           "status" => true
        ]);
    }

    public function favoriteToggle()
    {

        $post = Request::post("post");
        $post = json_decode($post);

        if (!$this->checkQuery($post)) {

            return $this->false;
        }

        $ip = System::getUserIP();

        $product = CloudStore::$app->store->loadOne("favorites", ["product" => $post->id, "ip" => $ip]);

        if ($product) {

            $isFavorite = false;
            $result = CloudStore::$app->store->delete("favorites", ["product" => $post->id, "ip" => $ip]);
        } else {

            $isFavorite = true;
            $result = CloudStore::$app->store->collect("favorites", ["product" => $post->id, "ip" => $ip]);
        }

        if ($result) {

            return json_encode([
               "status" => true,
               "isFavorite" => $isFavorite
            ]);
        }

        return $this->false;
    }

    public function setRating()
    {

        $post = Request::post("post");
        $post = json_decode($post);

        if (!$this->checkQuery($post)) {

            return $this->false;
        }

        $ip = System::getUserIP();

        if (empty($post->max)|| empty($post->id)) {

            return $this->false;
        }

        $rating = $post->max + 1;
        if ($rating < 1 || $rating > 5) {

            return $this->false;
        }

        if (S::loadOne("reviews", ["ip" => $ip, "product" => $post->id])
        || !S::loadOne("products", ["id" => $post->id])) {

            return $this->false;
        }

        $result = CloudStore::$app->store->collect("reviews", ["ip" => $ip, "product" => $post->id, "rating" => $rating]);
        if (!$result) {

            return $this->false;
        }

        $result = ProductManager::updateRating($post->id);
        if (!$result) {

            return $this->false;
        }

        return $this->true;
    }

    public function getAmount()
    {

        $productsAmount = CloudStore::$app->store->count("products");
        return json_encode([
           "status" => true,
           "productsAmount" => $productsAmount
        ]);
    }

    // @todo move it to AjaxRouter
    private function checkQuery($post) : bool
    {

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {

            return false;
        }

        if (!$post) {

            return false;
        }

        if (empty($post->csrf) || !Utils::validateToken($post->csrf)) {

            return false;
        }

        return true;

    }
}