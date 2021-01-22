<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 10.09.2019
 * Time: 22:58
 */

namespace Jet\App\Engine\Config;

use Jet\PHPJet;

/**
 * Class Filler
 * @package Jet\App\Engine\Config
 */
class ConfigManager
{
    public function prepareConfig(): void
    {
        $db = database::getInstance();

        // load data from db
        $this->loadSettings();
    }

    private function loadSettings(): void
    {
        $domain = PHPJet::$app->router->getDomain();
        // search by domain
        $config = PHPJet::$app->store->execGet("SELECT * FROM _config WHERE domain = :domain", [':domain' => $domain]);
        if (!isset($config[0])) {
            PHPJet::$app->exit('Website ' . $domain . ' does not exist on the server. Please, contact administrator.');
        }

        // it's very important to load site_id before any other query through Store or ActiveRecord
        $config = $config[0];
        Config::$config['site_id'] = $config['id'];

        // set session variable for views
        $sessionVariableName = PHPJet::$app->store->getPartitionColumnName();
        PHPJet::$app->store->execSet("SET @{$sessionVariableName} = :id", [':id' => Config::$config['site_id']]);

        // load page builder settings only for client
        Config::$pageBuilder['client']['active'] = (bool)PHPJet::$app->system->settings->getContext('pagebuilder');
    }
}