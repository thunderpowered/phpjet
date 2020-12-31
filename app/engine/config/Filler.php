<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 10.09.2019
 * Time: 22:58
 */

namespace CloudStore\App\Engine\Config;

use CloudStore\CloudStore;

/**
 * Class Filler
 * @package CloudStore\App\Engine\Config
 */
class Filler
{
    public function prepareConfig(): void
    {
        $db = database::getInstance();

        // load data from db
        $this->loadSettings();
    }

    private function loadSettings(): void
    {
        $domain = CloudStore::$app->router->getDomain();
        // search by domain
        $config = CloudStore::$app->store->execGet("SELECT * FROM _config WHERE domain = :domain", [':domain' => $domain]);
        if (!isset($config[0])) {
            CloudStore::$app->exit('Website ' . $domain . ' does not exist on the server. Please, contact administrator.');
        }

        // it's very important to load site_id before any other query through Store or ActiveRecord
        $config = $config[0];
        Config::$config['site_id'] = $config['id'];

        // set session variable for views
        $sessionVariableName = CloudStore::$app->store->getPartitionColumnName();
        CloudStore::$app->store->execSet("SET @{$sessionVariableName} = :id", [':id' => Config::$config['site_id']]);

        // load theme
        $theme = CloudStore::$app->system->settings->getContext('theme');
        if (!$theme) {
            CloudStore::$app->exit('No theme to load. Open CloudStore Administrator Panel and select the theme.');
        }
        Config::$activeTheme = Config::$availableThemes[$theme];
    }
}