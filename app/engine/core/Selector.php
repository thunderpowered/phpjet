<?php

namespace CloudStore\App\Engine\Core;

use CloudStore\CloudStore;

/**
 *
 * Select the config.
 *
 * ShopEngine is multi-site engine. If you want to create new site, just put your own config into engine/config/your_domain.ex/config.php directory.
 *
 * your_domain.ex - It's your domain.
 * You can only have 1 site per each domain.
 *
 */

/**
 * Class Selector
 * @package CloudStore\App\Engine\Core
 */
class Selector
{
    /**
     * @var string
     */
    private $site;

    /**
     * Selector constructor.
     */
    public function __construct()
    {
        $this->site = CloudStore::$app->system->request->getSERVER('HTTP_HOST');
    }

    public function select()
    {
        if (!file_exists(ENGINE . 'config/' . $this->site . '/')) {

            define("CONFIG_DIR", ENGINE . 'config/default/');
            return;
        }

        define("CONFIG_DIR", ENGINE . 'config/' . $this->site . '/');
    }
}
