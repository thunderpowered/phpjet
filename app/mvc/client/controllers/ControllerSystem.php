<?php


namespace CloudStore\App\MVC\Client\Controllers;


use CloudStore\App\Engine\Core\Controller;
use CloudStore\CloudStore;

/**
 * Class TaskController
 * @package CloudStore\App\MVC\Client\Controllers
 * This controller is only for system purposes
 */
class ControllerSystem extends Controller
{
    /**
     * @var string
     */
    private $accessToken = '~h?C~%|rkuT9~~oek{vkF9CpcKBuZIAC';

    /**
     * TaskController constructor.
     * @param string $name
     */
    public function __construct(string $name = "")
    {
        parent::__construct($name);
        $accessToken = CloudStore::$app->system->request->getGET('accessToken');
        if (!$accessToken || $accessToken !== $this->accessToken) {
            CloudStore::$app->router->goHome();
        }
    }

    /**
     * @return bool
     */
    public function actionCacheManager(): bool
    {
        return CloudStore::$app->tool->cache->manageCache();
    }
}