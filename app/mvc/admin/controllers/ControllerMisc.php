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

    public function __construct(string $name = "")
    {
        parent::__construct($name, false);
        $this->modelAdmin = new ModelAdmin();
    }

    /**
     * @return string
     */
    public function actionGetLogotype(): string
    {
        // todo move to model
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

    /**
     * @return string
     */
    public function actionSetWallpaper(): string
    {
        // Should check every action that require admin to be authorized
        if (!$this->modelAdmin->isAdminAuthorized()) {
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('Not authorized');

            $this->modelAdmin->recordActions('Theme', false, 'attempt to change wallpapers failed - not authorized');
            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
        }

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
        // Should check every action that require admin to be authorized
        if (!$this->modelAdmin->isAdminAuthorized()) {
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('Not authorized');

            $this->modelAdmin->recordActions('Panel Mode', false, 'attempt to change mode failed - not authorized');
            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
        }

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
        // Should check every action that require admin to be authorized
        if (!$this->modelAdmin->isAdminAuthorized()) {
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('Not authorized');
            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
        }

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
        // Should check every action that require admin to be authorized
        if (!$this->modelAdmin->isAdminAuthorized()) {
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('Not authorized');

            $this->modelAdmin->recordActions('Panel Mode', false, 'attempt to set default window failed - not authorized');
            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
        }

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
        // Should check every action that require admin to be authorized
        if (!$this->modelAdmin->isAdminAuthorized()) {
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('Not authorized');
            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
        }

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