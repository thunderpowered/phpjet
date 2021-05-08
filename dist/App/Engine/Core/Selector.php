<?php

namespace Jet\App\Engine\Core;

use Jet\App\Engine\Exceptions\CoreException;
use Jet\PHPJet;

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
 * @package Jet\App\Engine\Core
 * @deprecated
 */
class Selector
{
    /**
     * @var string
     */
    private $site;

    /**
     * Selector constructor.
     * @deprecated
     */
    public function __construct()
    {
        if (PHPJet::$app && PHPJet::$app->system) {
            $this->site = PHPJet::$app->system->request->getSERVER('HTTP_HOST');
        } else {
            $this->site = 'default';
        }
    }

    /**
     * @throws CoreException
     * @deprecated
     */
    public function select()
    {
        if (!file_exists(ENGINE . 'config/' . $this->site . '/')) {
            PHPJet::$app->exit("Config '$this->site' does not exist");
        } else {
            define("CONFIG_DIR", ENGINE . 'config/' . $this->site . '/');
        }
    }

    /**
     * @deprecated
     * temporary
     */
    public static function __legacySelect()
    {
        define("CONFIG_DIR", ENGINE . 'config/default/');
    }
}
