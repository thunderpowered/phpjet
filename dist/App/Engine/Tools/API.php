<?php


namespace Jet\App\Engine\Tools;


use Jet\App\Engine\Config\Config;

/**
 * Class API
 * @package Jet\App\Engine\Tools
 * @deprecated
 */
class API
{
    /**
     * API constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return \Google_Service_Sheets
     * @deprecated
     */
    public static function getGoogleApiService(): \Google_Service_Sheet
    {
        if (!getenv("GOOGLE_APPLICATION_CREDENTIALS")) {
            putenv("GOOGLE_APPLICATION_CREDENTIALS=" . WEB . "files/google_api/" . Config::$config["site_id"] . "/sheetsapi.json");
        }

        $client = new \Google_Client();
        $client->setApplicationName("Enginecom");
        $client->useApplicationDefaultCredentials();
        $client->addScope('https://www.googleapis.com/auth/drive');

        $service = new \Google_Service_Sheets($client);
        return $service;
    }
}