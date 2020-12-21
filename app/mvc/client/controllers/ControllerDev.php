<?php

namespace CloudStore\App\Engine\Controllers;

use CloudStore\App\Engine\Components\Utils;
use CloudStore\App\Engine\Components\S;
use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\Engine\Core\System;

class ControllerDev extends Controller
{

    public function type()
    {
        return 'act';
    }

    public function actionGoogleTest()
    {

        return false;
        $key = "1Q8nzF0Klz9lpWXGcK5uBPsVGKDbRbkDn1gN7coKTxvA";
        $service = Utils::getGoogleApiService();
        $n = $service->spreadsheets_values->get($key, "G1")->values[0][0];
        var_dump($n);
        exit();


        $range = new \Google_Service_Sheets_ValueRange();
        $range->setValues(["values" => ["test"]]);
        $conf = ["valueInputOption" => "RAW"];
        $result = $service->spreadsheets_values->update("1Q8nzF0Klz9lpWXGcK5uBPsVGKDbRbkDn1gN7coKTxvA", "A3", $range, $conf);
        var_dump($result);
    }

    public function actionmake_tables()
    {

        // DO NOT USE IT!

        return;

        $key = \CloudStore\App\Engine\Components\Request::get('key');

        if ($key !== "1m983-0xv4hn") {
            return false;
        }

        Utils::devMakeNewFilters();
        exit();
    }

    public function actionmake_images()
    {

        // DO NOT USE IT!

        return;

        $key = \CloudStore\App\Engine\Components\Request::get('key');

        if ($key !== "1m983-0xv4hn") {
            return false;
        }

        $db = database::getInstance();

        $images = \CloudStore\App\Engine\Components\Getter::getFreeData("SELECT * FROM products");

        foreach ($images as $image) {

            $new_string = str_replace("products_img/", "", $image['image']);

            //var_dump($new_string);
            $db->prepare("UPDATE products SET image = :string WHERE id = :id")->execute([
                ":id" => $image['id'],
                ":string" => $new_string
            ]);
        }

        exit();
    }

    public function actionnew_products()
    {

        return;

        $categories = CloudStore::$app->store->load("category", [], [], [], false);

        foreach ($categories as $category) {

            $parent = $category["parent"];

            if ($parent) {
                $parent_category = CloudStore::$app->store->loadOne("z_dev_temp_categories", ["id" => $parent]);
                $needle_category = CloudStore::$app->store->loadOne("category", ["category_handle" => $parent_category["uri"]]);

                if ($needle_category) {
                    CloudStore::$app->store->update("category", ["parent" => $needle_category["category_id"]], ["category_id" => $category["category_id"]]);
                }
            }
        }

        return;

        if (!empty($_SESSION["TEST"])) {
            return false;
        }

        $categories = CloudStore::$app->store->load("z_dev_temp_categories", [], [], [], false);

        foreach ($categories as $category) {
            CloudStore::$app->store->collect("category", [
                "name" => $category["name"],
                "category_handle" => $category["uri"],
                "category_description" => $category["shorttext"],
                "parent" => $category["parent_id"]
            ]);

            $category_id = database::getInstance()->lastInsertId();

            $products = CloudStore::$app->store->load("z_dev_temp_products", ["parent_id" => $category["id"]], [], [], false);

            $i = 0;

            foreach ($products as $product) {

                if (file_exists(WEB . 'uploads/temp/' . $product["picture"])) {

                    $image = file_get_contents(WEB . 'uploads/temp/' . $product["picture"]);

                    $file_ex = pathinfo(WEB . 'temp/' . $product["picture"])['extension'];
                    $filename = $i . '-' . \CloudStore\App\Engine\Config\Config::$config['site_handler'] . '.' . $file_ex;

                    $full = 'astris/' . $filename;

                    $param = [
                        'http_key' => \CloudStore\App\Engine\Config\Config::$config['http_key'],
                        'img_name' => $filename,
                        'img_path' => "astris",
                        'img' => base64_encode($image)
                    ];

                    $result = Utils::cURLCall(Router::getHost() . '/ajax/service/image', $param);
                }

                CloudStore::$app->store->collect("products", [
                    "title" => $product["name"],
                    "price" => $product["price"],
                    "category_id" => $category_id,
                    "description" => $product["content"],
                    "avail" => $product["visibility"],
                    "quantity_stock" => 1,
                    "image" => $full
                ]);

                $id = database::getInstance()->lastInsertId();
                CloudStore::$app->store->update("products", ["handle" => $id . "-" . Utils::makeHandle($product["name"])], ["id" => $id]);

                if (file_exists(WEB . 'uploads/temp/' . $product["picture"])) {
                    CloudStore::$app->store->collect("GALLERY", ["id" => $id, "add_img_link" => $full]);
                }


                $i++;
            }
        }

        $_SESSION["TEST"] = true;
        exit();
    }

    public function actionload_products()
    {

    }
}
