<?php

namespace CloudStore\App\Engine\Ajax\Handlers;

class AjaxUpdate
{

    public $hash;
    public $dirs;

    public function update()
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
        $this->dirs['handlers'] = scandir("../engine/handlers/");
        $this->dirs['javascript'] = scandir("../engine/javascript/");

        //Add files
        $this->addFile($this->dirs['models'], 'models');
        $this->addFile($this->dirs['core'], 'core');
        $this->addFile($this->dirs['components'], 'components');
        $this->addFile($this->dirs['controllers'], 'controllers');
        $this->addFile($this->dirs['handlers'], 'handlers');
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

    public function addFile($array, $dir)
    {
        for ($i = 2, $c = count($array); $i < $c; $i++) {

            if (!file_exists('../engine/updates/temp/' . $dir . '/')) {
                mkdir('../engine/updates/temp/' . $dir . '/');
            }
            copy('../engine/' . $dir . '/' . $array[$i], '../engine/updates/temp/' . $dir . '/' . $array[$i]);
        }
    }

    public function deleteDir($dirPath)
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

    public function makeHash($site, $dirs)
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
