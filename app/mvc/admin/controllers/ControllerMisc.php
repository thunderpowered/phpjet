<?php


namespace CloudStore\App\MVC\Admin\Controllers;


use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\MVC\Admin\Models\ModelAdmin;
use CloudStore\CloudStore;

/**
 * Class ControllerMedia
 * @package CloudStore\App\MVC\Admin\Controllers
 */
class ControllerMisc extends Controller
{
    /**
     * @var array
     */
    protected $methods = [
        'POST'
    ];
    /**
     * @var ModelAdmin
     */
    private $modelAdmin;
    /**
     * @var string
     */
    private $contextKeyWallpaper = 'wallpaper';
    /**
     * @var string
     */
    private $contextKeyLogotype = 'logotype';

    public function __construct(string $name = "")
    {
        parent::__construct($name);
        $this->modelAdmin = new ModelAdmin();
    }

    /**
     * @return string
     */
    public function actionGetLogotype(): string
    {
        $logotype = CloudStore::$app->system->settings->getContext($this->contextKeyLogotype);
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

    /**
     * @return string
     */
    public function actionGetWallpaper(): string
    {
        // Should check every action that require admin to be authorized
        if (!$this->modelAdmin->isAdminAuthorized()) {
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('Not authorized');
            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
        }

        $wallpaper = $this->modelAdmin->getAdminContext($this->contextKeyWallpaper);
        if ($wallpaper) {
            $wallpaper = CloudStore::$app->tool->utils->getImageLink($wallpaper);
            CloudStore::$app->tool->JSONOutput->setStatusTrue();
            CloudStore::$app->tool->JSONOutput->setData([
                'wallpaper' => $wallpaper
            ]);
        } else {
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
        }

        return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
    }

    /**
     * @return string
     */
    public function actionGetTime(): string
    {
        if (!$this->modelAdmin->isAdminAuthorized()) {
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('Not authorized');
            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
        }

        CloudStore::$app->tool->JSONOutput->setStatusTrue();
        CloudStore::$app->tool->JSONOutput->setData([
            'serverTimeUTC' => time(),
            'serverTimeOffset' => (int)date('Z'),
            'serverTimeZone' => date_default_timezone_get()
        ]);
        return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
    }
}