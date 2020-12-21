<?php

namespace CloudStore\App\MVC\Client\Models;

use CloudStore\App\Engine\Components\S;
use CloudStore\App\Engine\Config\Database;
use CloudStore\App\Engine\Core\Model;
use CloudStore\App\Engine\Components\Utils;

class ModelFather extends Model
{

    public function deleteStore($post)
    {

        $store = CloudStore::$app->store->loadOne("stores", ["user" => $post["users_id"], "store" => $post["id"]]);

        if (empty($store)) {

            die("Forbidden");
        }

        // Drop table
        if (!S::delete("stores", ["user" => $post["users_id"], "store" => $post["id"]])) {

            echo json_encode([
                "error" => true
            ]);

            exit();
        }

        echo json_encode([
            "error" => false
        ]);

        exit();
    }

    public function editStore($post)
    {

        $store = CloudStore::$app->store->loadOne("stores", ["user" => $post["users_id"], "store" => $post["id"]]);

        if (empty($store)) {

            echo json_encode([
                "error" => true
            ]);

            exit();
        }

        CloudStore::$app->store->update("stores", [
            "name" => $post["name"],
            "handle" => Utils::makeHandle($post["name"]),
            "admin_email" => $post["email"],
            "smtp_email" => $post["smtp"],
            "smtp_password" => $post["smtp_password"]
        ], ["user" => $post["users_id"], "store" => $post["id"]]);

        if (!empty($post["domain"])) {

            CloudStore::$app->store->update("settings", ["domain" => $post["domain"]], ["user" => $post["users_id"], "store" => $post["id"]]);
        }

        echo json_encode([
            "error" => false
        ]);

        exit();
    }

    public function createStore($post)
    {

        // If something went wrong and config wasn't created, we need check it before making new.
        $this->checkDomain($post["store"], $post["domain"]);

        $handler = Utils::makeHandle($post["name"]);

        $id = (int)$post["id"];

        //$db_name = "db_" . $id;
        //$dbs = $this->get_dbs();

        /*
          while( in_array( $db_name , $dbs ) )
          {

          $db_name = "db_" . ++$id;

          }
         */

        $url = \CloudStore\App\Engine\Config\Config::$config["protocol"] . $post["domain"];

        $result = CloudStore::$app->store->collect("stores", [
            "domain" => $post["domain"],
            "admin_domain" => "admin." . $post["domain"],
            "user" => $post["id"],
            "store" => $post["store"],
            "name" => $post["name"],
            "handle" => $handler,
            "url" => $url,
            "smtp_email" => $post["smtp"],
            "smtp_password" => $post["smtp_password"],
            "admin_email" => $post["email"],
            "tariff_start" => $post["date"],
            "tariff_days" => $post["days"]
        ]);

        if ($result) {

            if ($this->fillDB($post["store"])) {
                echo json_encode([
                    "error" => false
                ]);

                exit();
            }

            /*

              $created = $this->fill_db( $db_name, $post, $handler );

              if( !$created )
              {

              echo json_encode( [

              "error" => $created,
              "db"    => $db_name

              ] );

              exit();


              }

             */


            //$final = $this->create_config( $post, $id, $post["store"] );
        }


        echo json_encode([
            "error" => true
        ]);

        exit();
    }

    public function checkDomain($store, $domain)
    {

        if (S::loadOne("stores", ["store" => $store]) OR CloudStore::$app->store->loadOne("stores", ["domain" => $domain])) {

            die("Forbidden");
        }

        return true;

        // OLD

        Database::getInstance()->query("USE enginecom");

        // Reload tables
        Database::showTables("enginecom");

        if (S::execGet("SELECT * FROM domains d LEFT JOIN stores s ON d.store = s.id WHERE d.name = :domain AND s.active = 1", [":domain" => $domain])) {

            die("Forbidden");
        }

        Database::getInstance()->query("USE father");

        // Reload tables again
        Database::showTables("father");

        return true;
    }

    public function fillDB($store)
    {

        // What is it, dude? Are you sure that you do not have easier way?
        // I'm thinking of filling this table dynamically
        // To add this only when user need it
        // But now i can't handle it

        CloudStore::$app->store->collect("settings", ["settings_name" => "delivery_packing_weight", "store" => $store]);

        CloudStore::$app->store->collect("settings", ["settings_name" => "help_popup_html", "store" => $store]);

        CloudStore::$app->store->collect("settings", ["settings_name" => "help_popup_link", "store" => $store]);

        CloudStore::$app->store->collect("settings", ["settings_name" => "help_popup_name", "store" => $store]);

        CloudStore::$app->store->collect("settings", ["settings_name" => "information_header_label_1", "store" => $store]);

        CloudStore::$app->store->collect("settings", ["settings_name" => "information_header_label_2", "store" => $store]);

        CloudStore::$app->store->collect("settings", ["settings_name" => "information_site_description", "store" => $store]);

        CloudStore::$app->store->collect("settings", ["settings_name" => "information_site_name", "store" => $store]);

        CloudStore::$app->store->collect("settings", ["settings_name" => "price_template",
            "settings_value" => 'a:5:{s:14:"initial_string";s:30:"{{amount_no_decimals}} руб.";s:8:"decimals";i:0;s:17:"decimal_delimiter";s:1:".";s:19:"thousands_delimiter";s:1:" ";s:14:"price_currency";s:7:"руб.";}',
            "store" => $store]);

        CloudStore::$app->store->collect("settings", ["settings_name" => "theme_colors", "store" => $store]);

        CloudStore::$app->store->collect("settings", ["settings_name" => "engine_version", "store" => $store]);

        CloudStore::$app->store->collect("settings", ["settings_section" => "social-links", "settings_name" => "facebook", "store" => $store]);

        CloudStore::$app->store->collect("settings", ["settings_section" => "social-links", "settings_name" => "twitter", "store" => $store]);

        CloudStore::$app->store->collect("settings", ["settings_section" => "social-links", "settings_name" => "telegram", "store" => $store]);

        CloudStore::$app->store->collect("settings", ["settings_section" => "social-links", "settings_name" => "vk", "store" => $store]);

        CloudStore::$app->store->collect("settings", ["settings_section" => "social-links", "settings_name" => "ok", "store" => $store]);

        CloudStore::$app->store->collect("settings", ["settings_section" => "social-links", "settings_name" => "instagram", "store" => $store]);

        CloudStore::$app->store->collect("settings", ["settings_section" => "social-links", "settings_name" => "vimeo", "store" => $store]);

        CloudStore::$app->store->collect("settings", ["settings_section" => "social-links", "settings_name" => "youtube", "store" => $store]);

        CloudStore::$app->store->collect("settings", ["settings_section" => "social-links", "settings_name" => "pinterest", "store" => $store]);

        // SLIDER TEMPORARY! JUST FOR DEMO
        //S::collect("dynamic_slider", ["slider_image" => "slider/slider2.jpeg", "slider_title" => "Заголовок", "slider_description" => "Не следует, однако забывать, что постоянный количественный рост и сфера нашей активности влечет за собой процесс внедрения и модернизации новых предложений. ", "slider_link" => "https://enginecom.io", "store" => $store]);

        //S::collect("dynamic_slider", ["slider_image" => "slider/slider1.jpeg", "slider_title" => "Заголовок", "slider_description" => "Не следует, однако забывать, что постоянный количественный рост и сфера нашей активности влечет за собой процесс внедрения и модернизации новых предложений. ", "slider_link" => "https://enginecom.io", "store" => $store]);

        //S::collect("dynamic_slider", ["slider_image" => "slider/slider3.jpg", "slider_title" => "Заголовок", "slider_description" => "Не следует, однако забывать, что постоянный количественный рост и сфера нашей активности влечет за собой процесс внедрения и модернизации новых предложений. ", "slider_link" => "https://enginecom.io", "store" => $store]);

        return true;
    }

    public function createConfig($post, $id, $store)
    {

        if (file_exists(ENGINE . "config/" . $post["domain"])) {

            // We've checked the store.
            //return false;
            $this->delFolder(ENGINE . "config/" . $post["domain"]);
        }

        mkdir(ENGINE . "config/" . $post["domain"]);

        $config = file_get_contents(ENGINE . "father/config.txt");

        $config_php = fopen(ENGINE . "config/" . $post["domain"] . "/config.php", "w");

        // Set info

        $config = str_replace("{{USER_ID}}", $id, $config);
        $config = str_replace("{{SHOP_ID}}", $store, $config);

        //fill file

        $result = fwrite($config_php, $config);

        fclose($config_php);

        return $result ? true : false;
    }

    public function delFolder($dir)
    {

        if (!file_exists($dir)) {

            return false;
        }

        $files = array_diff(scandir($dir), array('.', '..'));

        foreach ($files as $file) {

            (is_dir("$dir/$file")) ? delFolder("$dir/$file") : unlink("$dir/$file");
        }


        return rmdir($dir);
    }

    public function getDBs()
    {

        $stmt = database::getInstance()->query("SHOW DATABASES");
        $stmt->execute();

        $temp = $stmt->fetchAll();
        $dbs = [];

        foreach ($temp as $key => $value) {

            $dbs[] = $value["Database"];
        }

        return $dbs;
    }

    public function setPayment($post)
    {

        $id = $post["id"];

        if (!$stores = CloudStore::$app->store->load("stores", ["user" => $id])) {

            echo json_encode([
                "error" => true
            ]);

            exit();
        }

        CloudStore::$app->store->update("stores", ["tariff_start" => $post["date"], "tariff_days" => $post["days"]], ["user" => $id]);

        echo json_encode([
            "error" => false
        ]);

        exit();
    }
}
