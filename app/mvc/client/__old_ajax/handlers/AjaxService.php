<?php

namespace CloudStore\App\Engine\Ajax\Handlers;

use CloudStore\App\Engine\Components\Request;
use CloudStore\App\Engine\Components\S;
use CloudStore\App\Engine\Config\Config;
use CloudStore\App\Engine\Core\System;

class AjaxService
{

    private $filename = "common.css";
    private $filename_min = "common.min.css";
    private $hash;
    private $dirs;

    public function styles()
    {

        // @todo refactoring, divide by several methods instead of one

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $http_key = Request::post('http_key');
            $http_type = Request::post('http_type');

            $siteId = Request::post("site_id");
            if(!$siteId) {

                $siteId = Config::$config["site_id"];
            }

            $this->filename = WEB . "common/styles/" . $siteId . "/" . $this->filename;
            $this->filename_min = "common/styles/" . $siteId . "/" . $this->filename_min;

            if ($http_key !== Config::$config['http_key']) {

                return false;
            }

            // Create dir
            if(!file_exists(WEB . "common/") || !is_dir(WEB . "common/")) {

                mkdir(WEB . "common/");
            }

            // Create another dir
            if(!file_exists(WEB . "common/styles/") || !is_dir(WEB . "common/styles/")) {

                mkdir(WEB . "common/styles/");
            }

            // Create dir for site
            if(!file_exists(WEB . "common/styles/" . $siteId . "/") || !is_dir(WEB . "common/styles/" . $siteId . "/")) {

                mkdir(WEB . "common/styles/" . $siteId . "/");
            }

            if ($http_type === 'get') {

                $handle = @fopen($this->filename, "a+");

                if(!$handle) {

                    return false;
                }

                $file = fread($handle, filesize($this->filename));
                fclose($handle);

                return $file;
            } elseif ($http_type === 'write') {
                $content = Request::post('file_content');

                if (strlen($content) < 1) {

                    $content = "/* Empty */";
                }

                $handle = @fopen($this->filename, "w+");

                if(!$handle) {

                    return false;
                }

                $result = fwrite($handle, $content);

                fclose($handle);

                //Minify
                $handle_min = @fopen($this->filename_min, "w+");

                $result = fwrite($handle_min, $this->minify($content));

                fclose($handle_min);

                if (!$result) {

                    return false;
                }

                return true;
            }
        }

        return false;
    }

    public function minify(string $str)
    {

        $str = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $str);

        $str = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $str);

        if (strlen($str) < 1) {
            $str .= "/* common.min.css */";
        }

        return $str;
    }

    public function image()
    {
        if (\CloudStore\App\Engine\Components\Request::post('img')) {

            $img_str = Request::post('img');
            $key = Request::post('http_key');

            if ($key !== Config::$config['http_key']) {

                // @todo return json

                echo 400;
                exit();
            }

            $dir = \CloudStore\App\Engine\Components\Utils::makeHandle(\CloudStore\App\Engine\Components\Request::post('img_path'));
            $filename = \CloudStore\App\Engine\Components\Request::post('img_name');

            $_filename = explode(".", $filename);
            $extension = end($_filename);

            // Check extension
            if ($extension !== "jpg" AND
                $extension !== "jpeg" AND
                $extension !== "png" AND
                $extension !== "gif" AND
                $extension !== "bmp" AND
                $extension !== "ico") {

                echo 400;
                exit();
            }

            if ($dir) {

                if (file_exists(IMAGES . $dir)) {
                    $path = IMAGES . $dir . '/';
                } else {
                    mkdir(IMAGES . $dir . '/');
                    $path = IMAGES . $dir . '/';
                }
            } else {

                if (file_exists(IMAGES . 'Uncategorized/')) {
                    $path = IMAGES . 'Uncategorized/';
                } else {
                    mkdir(IMAGES . 'Uncategorized/');
                    $path = IMAGES . 'Uncategorized/';
                }
            }

            $image = $path . $filename;

            if (file_put_contents($image, base64_decode($img_str))) {

                if($extension === "ico") {

                    echo 200;
                    return true;
                }

                $imagine = new \Imagine\Gd\Imagine;

                //Image Resize
                $current = $imagine->open($image);
                if ($current->getSize()->getWidth() > 1920 OR $current->getSize()->getWidth() > 1920) {

                    $new_size = new \Imagine\Image\Box(1920, 1920);

                    $current->thumbnail($new_size)->save($image);
                }


                //Thumbnail
                $size = new \Imagine\Image\Box(250, 250);

                if (!$dir) {
                    $dir = "Uncategorized";
                }

                $thumb = THUMBNAILS . $dir . "/";
                if (!file_exists($thumb)) {
                    mkdir($thumb, 0777, true);
                }

                $imagine->open($image)->thumbnail($size)->save($thumb . $filename);


                echo 200;
            } else {

                echo 400;
            }

            exit();
        }
    }

    public function image_dropper()
    {
        if (isset($_POST['img_name']) AND $_SERVER['REQUEST_METHOD'] === 'POST') {
            $key = \CloudStore\App\Engine\Components\Request::post('http_key');

            if ($key !== \CloudStore\App\Engine\Config\Config::$config['http_key']) {
                return false;
            }

            $img_dir = IMAGES . \CloudStore\App\Engine\Components\Request::post('img_name');
            $thumb = THUMBNAILS . \CloudStore\App\Engine\Components\Request::post('img_name');

            if (file_exists($img_dir)) {
                @unlink($img_dir);
                @unlink($thumb);

                echo 200;
                exit();
            }

            echo 400;
            exit();
        }

        echo 500;
        exit();
    }

    public function fastOrder()
    {
        return json_encode(["status" => false]);

        // TODO: make it usable
        $csrf = Request::post("csrf");
        if (!Utils::validateToken($csrf)) {
            return json_encode(["status" => false]);
        }

        $params = Request::post("params");
        if (!$params) {
            return json_encode(["status" => false]);
        }

        $params = json_decode($params);
        $errors = [];

        // simple validation
        if (empty($params->email) || !Utils::validateEmail($params->email)) {
            $errors["email"] = true;
        }

        if (empty($params->name)) {
            $errors["name"] = true;
        }

        if (empty($params->phone)) {
            $errors["phone"] = true;
        }

        if ($errors) {
            return json_encode(["status" => false, "errors" => $errors]);
        }

        $comment = Utils::clear(($params->comment) ?? "Нет комментария");
        $params->name = Utils::clear($params->name);
        $params->phone = Utils::clear($params->phone);
        $params->email = Utils::clear($params->email);

        // Check product id
        if (empty($params->id)) {
            return json_encode(["status" => false]);
        }

        $product = CloudStore::$app->store->loadOne("products", ["id" => $params->id]);
        if (!$product) {
            return json_encode(["status" => false]);
        }
        $productLinkRaw = Router::getHost() . "/products/" . $product["id"];
        $productLink = "<a href='" . $productLinkRaw . "'>" . $product["title"] . "</a>";

        // Do something
        // @todo devide by several methods!
        $dt = new \DateTime("now", new \DateTimeZone('Europe/Moscow'));
        $time = $dt->format('H:i:s d:m:Y');

        $subject = "[" . Config::$config["site_name"] . "] Был совершён быстрый заказ в $time";
        $message = "<b>$subject</b><br/>Имя: $params->name<br/>Телефон: $params->phone<br/>Email: $params->email<br/>Комментарий: $comment<br/>Товар: $productLink (id: " . $product["id"] . ")";
        $messageMail = $message . "<br><br><i class='note'>Совет от команды Enginecom.io: постарайтесь ответить на звонок в течение 10 минут, так как за это время покупатель передумает с минимальной вероятностью.</i>";
        Utils::sendMail2(Config::$config["admin_email"], Config::$mail["email"], $subject, $messageMail);
        // basecamp chat
        // temp!
        // @todo find a solution for different stores
        if (Router::getHost() === "https://astris.ru") {
            Utils::cURLCall("https://3.basecamp.com/3181141/integrations/Y4QD6PaZnZGyKMMFmYmx31d5/buckets/8708001/chats/1237535631/lines", ["content" => $message]);
            return json_encode(["status" => true]);
        }
        Utils::cURLCall("https://3.basecamp.com/3181141/integrations/Y4QD6PaZnZGyKMMFmYmx31d5/buckets/9171237/chats/1309982481/lines", ["content" => $message]);

        // Google docs
        $key = "1Q8nzF0Klz9lpWXGcK5uBPsVGKDbRbkDn1gN7coKTxvA";
        $service = Utils::getGoogleApiService();
        $n = $service->spreadsheets_values->get($key, "G1")->values[0][0] + 1;
        $conf = ["valueInputOption" => "USER_ENTERED"];

        $range = new \Google_Service_Sheets_ValueRange();
        $range->setValues(["values" => [$productLinkRaw]]);
        $service->spreadsheets_values->update($key, "A" . $n, $range, $conf);

        $range->setValues(["values" => [$time]]);
        $service->spreadsheets_values->update($key, "B" . $n, $range, $conf);

        $range->setValues(["values" => [$params->name]]);
        $service->spreadsheets_values->update($key, "C" . $n, $range, $conf);

        $range->setValues(["values" => [$params->phone]]);
        $service->spreadsheets_values->update($key, "D" . $n, $range, $conf);

        $range->setValues(["values" => [$params->email]]);
        $service->spreadsheets_values->update($key, "E" . $n, $range, $conf);

        $range->setValues(["values" => [$comment]]);
        $service->spreadsheets_values->update($key, "F" . $n, $range, $conf);

        $range->setValues(["values" => [$n]]);
        $service->spreadsheets_values->update($key, "G1", $range, $conf);

        // Chatbot
        Utils::cURLCall("https://api.enginecom.io/bot/poterpite-informer/?type=server&api_key=82676b2cfa5dccb299374c3d5e2d79c87106d397e46420fb1357c1f402ac49ae", ["message" => str_replace("<br/>", "\n", $message)]);

        return json_encode(["status" => true]);
    }

    public function update_manager()
    {
        echo "\r\nSTARTING UPDATE... \r\n";

        if ($_SERVER['REQUEST_METHOD'] !== "POST") {
            return false;
        }

        $key = \CloudStore\App\Engine\Components\Request::post('http_key');
        if ($key !== \CloudStore\App\Engine\Config\Config::$config['http_key']) {
            return false;
        }

        //Using SSH
        //$conn = ssh2_connect("alexanpm.beget.tech");
        //ssh2_auth_password($conn, "alexanpm", "3Qum3r5z");
        //...
        //Using cURL
        //@chmod(ENGINE."updates/", 777);

        if (!file_exists(ENGINE . 'updates/')) {
            mkdir(ENGINE . 'updates/');
        }

        if (file_exists(ENGINE . 'updates/last_update.zip')) {
            unlink(ENGINE . 'updates/last_update.zip');
        }

        //$zip = new ZipArchive();
        $filename = "../engine/updates/temp/";

        if (!file_exists($filename)) {
            mkdir($filename);
        }

//        if($zip->open($filename, ZipArchive::CREATE) !== true) {
//            exit("Unagle to open $filename \n");
//        }
        //We update 4 dirs
        $this->dirs['models'] = scandir("../engine/models/");
        $this->dirs['core'] = scandir("../engine/core/");
        $this->dirs['components'] = scandir("../engine/components/");
        $this->dirs['controllers'] = scandir("../engine/controllers/");
        $this->dirs['javascript'] = scandir("../engine/javascript/");

        //Add files
        $this->addFile($this->dirs['models'], 'models');
        $this->addFile($this->dirs['core'], 'core');
        $this->addFile($this->dirs['components'], 'components');
        $this->addFile($this->dirs['controllers'], 'controllers');
        $this->addFile($this->dirs['javascript'], 'javascript');

        $newfilename = "../engine/updates/last_update.zip";

        //Set Password
        @system("zip -r " . $newfilename . " " . $filename . "*");

        if (file_exists($filename)) {
            $this->deleteDir($filename);
        }

        //Send files
        for ($i = 0, $c = count(\CloudStore\App\Engine\Config\Config::$sites); $i < $c; $i++) {

            $trans_key = $this->makeHash(\CloudStore\App\Engine\Config\Config::$sites[$i], $this->dirs);

            $this->hash = null;

            $query = [
                'http_key' => \CloudStore\App\Engine\Config\Config::$config['http_key'],
                'file' => new \CURLFile($newfilename, null, 'last_update.zip'),
                'trans_key' => $trans_key
            ];

            $ch = curl_init();
            $url = \CloudStore\App\Engine\Config\Config::$sites[$i]['app_name'] . '/connect/update';

            echo "SEND TO " . \CloudStore\App\Engine\Config\Config::$sites[$i]['app_name'] . '/connect/update   ';

            //Add HTTPS!
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

            $result = curl_exec($ch);

            echo "RESULT: \r\n";
            var_dump($result);

            curl_close($ch);
        }

        return true;

        //@chmod(ENGINE."updates/", 700);
    }

    private function addFile($array, $dir)
    {
        for ($i = 2, $c = count($array); $i < $c; $i++) {

            if (!file_exists('../engine/updates/temp/' . $dir . '/')) {
                mkdir('../engine/updates/temp/' . $dir . '/');
            }
            if (!empty($array[$i])) {
                copy('../engine/' . $dir . '/' . $array[$i], '../engine/updates/temp/' . $dir . '/' . $array[$i]);
            }
        }
    }

    private function deleteDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            return false;
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);

        return true;
    }

    private function makeHash($site, $dirs)
    {

        asort($dirs);

        foreach ($dirs as $key => $value) {

            for ($i = 2, $c = count($value); $i < $c; $i++) {

                $content = $this->hash . file_get_contents('../engine/' . $key . '/' . $value[$i]) . $site['app_key'];

                $this->hash = hash("sha256", $content);
            }
        }

        return $this->hash;
    }
}
