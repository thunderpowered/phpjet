<?php


namespace CloudStore\App\MVC\Admin\Controllers;


use CloudStore\App\Engine\Core\Controller;
use CloudStore\CloudStore;

/**
 * Class ControllerMedia
 * @package CloudStore\App\MVC\Admin\Controllers
 */
class ControllerMedia extends Controller
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
    public function actionGetLogotype(): string
    {
        $logotype = CloudStore::$app->system->settings->getContext('logotype');
        if ($logotype) {
            $logotype = CloudStore::$app->tool->utils->getImageLink($logotype);
            CloudStore::$app->tool->JSONOutput->setStatusTrue();
            CloudStore::$app->tool->JSONOutput->setData([
                'logotype' => $logotype
            ]);
        } else {
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('Logotype not found');
        }

        return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
    }
}