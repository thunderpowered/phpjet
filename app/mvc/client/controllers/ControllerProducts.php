<?php

namespace CloudStore\App\Engine\Controllers;

use CloudStore\App\Engine\Components\ProductManager;
use CloudStore\App\Engine\Components\S;
use CloudStore\App\Engine\Config\Config;
use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\Engine\Components\Utils;
use CloudStore\App\Engine\Core\System;

class ControllerProducts extends Controller
{

    public $products;

    public function actionBasic()
    {
        //$id = ShopEngine::getRoute()[2];

        $id = intval(Router::getAction());

        //Selection by ID!
        $sql = "SELECT * FROM products WHERE id=? AND avail='1' AND store = ? ";

        $this->products = ProductManager::loadExec($sql, [$id, Config::$config["site_id"]], 1, false, true);
        if (!$this->products) {
            //Redirect if no ID

            return $this->model->handler();
        }

        $this->products = $this->products[0];

        $this->model->updateCount($this->products);

        $this->title = $this->products['category'] . ( $this->products['category'] ? " - " : "" ) . $this->products['brand'] . ' ' . $this->products['title'] . ' - ' . $this->products['price'];

        $gallery = CloudStore::$app->store->load("GALLERY", ["id" => $this->products['id']], ["add_img_id" => "DESC"]);

        return $this->view->render($this->view->getTemplateName(), [
            'product' => $this->products,
            'gallery' => $gallery
        ]);
    }

    public function actionFavorite()
    {

        //$products = Products::load(["avail" => "!0", "price" => "!0.00", "favorite" => 1], 36);
        $products = ProductManager::loadExec("SELECT * FROM favorites f INNER JOIN products p ON f.product = p.id AND f.store = p.store WHERE f.store = ? AND ip = ? ", [Config::$config["site_id"], System::getUserIP()]);
        if (!$products) {

            Router::hoHome();
        }

        return $this->view->render("view_favorite", [
            "products" => $products
        ]);
    }

    // @todo move to model all non-action methods!
    public function GetLink()
    {
        $route = Router::getRoute();
        return '/' . $route[1] . '/' . $route[2];
    }

    /**
     * @return string
     */
    public function getURLCanonical(): string
    {
        return '<link rel="canonical" href="' . Router::getHost() . '/products/' . Utils::makeHandle($this->products['handle'], true) . '">';
    }

    /**
     * @return array
     */
    public function SEO(): array
    {
        $product = $this->products;

        $widht = null;
        $height = null;

        $imagine = new \Imagine\Gd\Imagine;

        if (false && file_exists(IMAGES . $product['image_link']) AND !is_dir(IMAGES . $product['image_link'])) {
            $size = @$imagine->open(IMAGES . $product['image_link'])->getSize();

            if ($size) {

                $width = $size->getWidth();
                $height = $size->getHeight();
            }
        }

        $description = $product['description'];

        $description = preg_replace("|<h\d>(.+)</h\d>|isU", '', $description);

        return [
            'property' => [
                'og:type' => 'product',
                'og:title' => $this->products['category'] . ' - ' . $product['brand'] . ' ' . $product['title'] . ' - ' . Utils::asPrice($product['price_int']) . ' ' . ($product['products_sku'] !== '' ? $product['products_sku'] . '. ' : ''),
                'og:image' => Router::getHost() . '/' . IMAGES . $product['image_link'],
                'og:image:width' => $width ?? '',
                'og:image:height' => $height ?? '',
                'og:image:secure_url' => Router::getHost() . '/' . IMAGES . $product['image_link'],
                'og:description' => Utils::removeSpecialChars($description),
                'og:price:amount' => $product['price_int'],
                'og:price:currency' => 'RUB',
                'og:url' => Router::getHost() . $_SERVER['REQUEST_URI'],
                'og:site_name' => \CloudStore\App\Engine\Config\Config::$config['site_name'],
                'fb:app_id' => 220746618429343
            ],
            'name' => [
                'keywords' => 'none',
                'description' => 'Купить &lt;em&gt;' . $product['title'] . '&lt;em&gt;: цена ' . Utils::asPrice($product['price_int']) . '; Продажа с доставкой по Москве и другим городам.',
                'twitter:site' => '@' . \CloudStore\App\Engine\Config\Config::$config['site_handler'],
                'twitter:card' => 'summary',
                'twitter:title' => $this->products['category'] . ' - ' . $product['brand'] . ' ' . $product['title'] . ' - ' . Utils::asPrice($product['price_int']) . ' ' . ($product['products_sku'] !== '' ? $product['products_sku'] . '. ' : ''),
                'twitter:description' => Utils::removeSpecialChars($description),
                'twitter:image' => Router::getHost() . '/' . IMAGES . $product['image_link'],
                'twitter:image:width' => $width ?? '',
                'twitter:image:height' => $width ?? ''
            ]
        ];
    }
}
