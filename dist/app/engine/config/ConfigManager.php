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
        // just a temp code, i need to make it work asap, so i'll do it better later
        $config = PHPJet::$app->store->execGet("select *, if (admin_domain = :domain1, true, false) as admin from _config where (domain = :domain2 or admin_domain = :domain3) AND domain != admin_domain", [':domain1' => $domain, ':domain2' => $domain, ':domain3' => $domain]);
        if (empty($config[0])) {
            PHPJet::$app->exit('Website ' . $domain . ' does not exist on the server. Please, contact administrator.');
        }

        // it's very important to load site_id before any other query through Store or Table
        $config = $config[0];
        Config::$config['site_id'] = $config['id'];
        Config::$config['admin'] = $config['admin'];

        // set session variable for views
        $sessionVariableName = PHPJet::$app->store->getPartitionColumnName();
        PHPJet::$app->store->execSet("SET @{$sessionVariableName} = :id", [':id' => Config::$config['site_id']]);

        // load page builder settings only for client
        Config::$pageBuilder['client']['active'] = (bool)PHPJet::$app->system->settings->getContext('pagebuilder');
    }
}