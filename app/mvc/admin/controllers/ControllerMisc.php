<?php


namespace CloudStore\App\MVC\Admin\Controllers;


use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\MVC\Admin\Models\ModelAdmin;
use CloudStore\CloudStore;
use http\Exception\BadQueryStringException;

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
    private $contextKeyLogotype = 'logotype';

    /**
     * ControllerMisc constructor.
     * @param string $name
     * @param bool $enableTracker
     */
    public function __construct(string $name = "", bool $enableTracker = false)
    {
        parent::__construct($name, $enableTracker);
        $this->modelAdmin = new ModelAdmin();

        if (!$this->modelAdmin->isAdminAuthorized()) {
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('Not authorized');
            $output = CloudStore::$app->tool->JSONOutput->returnJSONOutput();

            $this->modelAdmin->recordActions('Auth', false, 'Unauthorized query registered');
            // force application to send output and stop
            CloudStore::$app->router->immediateResponse($output);
        }
    }

    /**
     * @return string
     */
    public function actionGetWallpaper(): string
    {
        $wallpaper = $this->modelAdmin->getAdminWallpaper();
        if ($wallpaper) {
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
        CloudStore::$app->tool->JSONOutput->setStatusTrue();
        CloudStore::$app->tool->JSONOutput->setData([
            'serverTimeUTC' => time(),
            'serverTimeOffset' => (int)date('Z'),
            'serverTimeZone' => date_default_timezone_get()
        ]);
        return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
    }

    /**
     * @return string
     */
    public function actionSetWallpaper(): string
    {
        $file = CloudStore::$app->system->request->getFile('file');
        if (!$file) {
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('No file to load');

            $this->modelAdmin->recordActions('Theme', false, 'attempt to change wallpapers failed - no data');
            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
        }

        $wallpaper = $this->modelAdmin->setAdminWallpaper($file);
        if (!$wallpaper) {
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('Unable to set a Wallpaper');

            $this->modelAdmin->recordActions('Theme', false, 'attempt to change wallpapers failed - function returned false');
            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
        }

        // everything is fine
        CloudStore::$app->tool->JSONOutput->setStatusTrue();
        CloudStore::$app->tool->JSONOutput->setMessageBoxText('New Wallpaper set');
        CloudStore::$app->tool->JSONOutput->setData([
            'wallpaper' => $wallpaper
        ]);

        $this->modelAdmin->recordActions('Theme', true, 'attempt to change wallpapers succeeded');
        return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
    }

    /**
     * @return string
     */
    public function actionSetMode(): string
    {
        $mode = CloudStore::$app->system->request->getPOST('panelMode');
        if (!$mode) {
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('No data to set');

            $this->modelAdmin->recordActions('Panel Mode', false, 'attempt to change mode failed - no data');
            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
        }

        $result = $this->modelAdmin->setPanelState($mode);
        if (!$result) {
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('Unable to set Panel Mode');

            $this->modelAdmin->recordActions('Panel Mode', false, 'attempt to change mode failed - function returned false');
            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
        }

        // everything is fine
        CloudStore::$app->tool->JSONOutput->setStatusTrue();
        CloudStore::$app->tool->JSONOutput->setMessageBoxText('Panel Mode successfully set');
        CloudStore::$app->tool->JSONOutput->setData([
            'panelMode' => $mode
        ]);

        $this->modelAdmin->recordActions('Panel Mode', true, 'attempt to change mode succeeded');
        return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
    }

    /**
     * @return string
     */
    public function actionGetMode(): string
    {
        $mode = $this->modelAdmin->getPanelState();
        if (!$mode) {
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
        }

        CloudStore::$app->tool->JSONOutput->setStatusTrue();
        CloudStore::$app->tool->JSONOutput->setData([
            'panelMode' => $mode
        ]);
        return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
    }

    /**
     * @return string
     */
    public function actionSetDefaultWindow(): string
    {
        $defaultWindow = CloudStore::$app->system->request->getPOST('defaultWindow');
        if (!is_int($defaultWindow)) {
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('No data to set');

            $this->modelAdmin->recordActions('Panel Mode', false, 'attempt to set default window failed - no data');
            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
        }

        $result = $this->modelAdmin->setDefaultWindow($defaultWindow);
        if (!$result) {
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('Unable to set default window');

            $this->modelAdmin->recordActions('Panel Mode', false, 'attempt to set default window failed - function returned false');
            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
        }

        // everything is fine
        CloudStore::$app->tool->JSONOutput->setStatusTrue();
        CloudStore::$app->tool->JSONOutput->setMessageBoxText('Default window was set');
        CloudStore::$app->tool->JSONOutput->setData([
            'defaultWindow' => $defaultWindow
        ]);

        $this->modelAdmin->recordActions('Panel Mode', true, 'attempt to set default window succeeded');
        return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
    }

    /**
     * @return BadQueryStringException
     */
    public function actionGetDefaultWindow(): string
    {
        $defaultWindow = $this->modelAdmin->getDefaultWindow();
        if ($defaultWindow < 0) {
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
        }

        CloudStore::$app->tool->JSONOutput->setStatusTrue();
        CloudStore::$app->tool->JSONOutput->setData([
            'defaultWindow' => $defaultWindow
        ]);
        return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
    }
}