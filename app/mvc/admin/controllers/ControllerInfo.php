<?php


namespace CloudStore\App\MVC\Admin\Controllers;


use CloudStore\App\Engine\Core\Controller;
use CloudStore\CloudStore;

/**
 * Class ControllerInfo
 * @package CloudStore\App\MVC\Admin\Controllers
 */
class ControllerInfo extends Controller
{
    /**
     * @var array
     */
    protected $methods = [
        'POST'
    ];

    /**
     * @return string
     */
    public function actionEngineVersion(): string
    {
        $engineVersion = CloudStore::$app->system->getEngineVersion();
        $engineVersion = 'Engine Version: ' . $engineVersion;
        CloudStore::$app->tool->JSONOutput->setStatusTrue();
        CloudStore::$app->tool->JSONOutput->setData([
            'engineVersion' => $engineVersion
        ]);

        return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
    }
}