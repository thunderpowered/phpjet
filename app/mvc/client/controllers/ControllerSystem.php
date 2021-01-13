<?php


namespace Jet\App\MVC\Client\Controllers;


use Jet\App\Engine\Core\Controller;
use Jet\PHPJet;

/**
 * Class TaskController
 * @package Jet\App\MVC\Client\Controllers
 * This controller is only for system purposes
 */
class ControllerSystem extends Controller
{
    /**
     * @var string
     */
    private $accessToken = '~h?C~%|rkuT9~~oek{vkF9CpcKBuZIAC';

    /**
     * ControllerSystem constructor.
     * @param string $name
     * @param bool $enableTracker
     */
    public function __construct(string $name = "", bool $enableTracker = false)
    {
        parent::__construct($name, $enableTracker);
        $accessToken = PHPJet::$app->system->request->getGET('accessToken');
        if (!$accessToken || $accessToken !== $this->accessToken) {
            PHPJet::$app->router->goHome();
        }
    }

    /**
     * @return bool
     */
    public function actionCacheManager(): bool
    {
        return PHPJet::$app->tool->cache->manageCache();
    }
}