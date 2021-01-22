<?php


namespace Jet\App\MVC\Admin\Controllers;


use Jet\App\Engine\Core\Controller;
use Jet\App\MVC\Admin\Models\ModelAdmin;
use Jet\PHPJet;
use http\Exception\BadQueryStringException;

/**
 * Class ControllerMedia
 * @package Jet\App\MVC\Admin\Controllers
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
     * @var bool
     */
    protected $tokenRequired = true;
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
            PHPJet::$app->tool->JSONOutput->setStatusFalse();
            PHPJet::$app->tool->JSONOutput->setMessageBoxText('Not authorized');
            $output = PHPJet::$app->tool->JSONOutput->returnJSONOutput();

            $this->modelAdmin->recordActions('Auth', false, 'Unauthorized query registered');
            // force application to send output and stop
            PHPJet::$app->router->immediateResponse($output);
        }
    }

    /**
     * @return string
     */
    public function actionGetWallpaper(): string
    {
        $wallpaper = $this->modelAdmin->getAdminWallpaper();
        if ($wallpaper) {
            PHPJet::$app->tool->JSONOutput->setStatusTrue();
            PHPJet::$app->tool->JSONOutput->setData([
                'wallpaper' => $wallpaper
            ]);
        } else {
            PHPJet::$app->tool->JSONOutput->setStatusFalse();
        }

        return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
    }

    /**
     * @return string
     */
    public function actionGetTime(): string
    {
        PHPJet::$app->tool->JSONOutput->setStatusTrue();
        PHPJet::$app->tool->JSONOutput->setData([
            'serverTimeUTC' => time(),
            'serverTimeOffset' => (int)date('Z'),
            'serverTimeZone' => date_default_timezone_get()
        ]);
        return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
    }

    /**
     * @return string
     */
    public function actionSetWallpaper(): string
    {
        $file = PHPJet::$app->system->request->getFile('file');
        if (!$file) {
            PHPJet::$app->tool->JSONOutput->setStatusFalse();
            PHPJet::$app->tool->JSONOutput->setMessageBoxText('No file to load');

            $this->modelAdmin->recordActions('Theme', false, 'attempt to change wallpapers failed - no data');
            return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
        }

        $wallpaper = $this->modelAdmin->setAdminWallpaper($file);
        if (!$wallpaper) {
            PHPJet::$app->tool->JSONOutput->setStatusFalse();
            PHPJet::$app->tool->JSONOutput->setMessageBoxText('Unable to set a Wallpaper');

            $this->modelAdmin->recordActions('Theme', false, 'attempt to change wallpapers failed - function returned false');
            return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
        }

        // everything is fine
        PHPJet::$app->tool->JSONOutput->setStatusTrue();
        PHPJet::$app->tool->JSONOutput->setMessageBoxText('New Wallpaper set');
        PHPJet::$app->tool->JSONOutput->setData([
            'wallpaper' => $wallpaper
        ]);

        $this->modelAdmin->recordActions('Theme', true, 'attempt to change wallpapers succeeded');
        return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
    }

    /**
     * @return string
     */
    public function actionSetMode(): string
    {
        $mode = PHPJet::$app->system->request->getPOST('panelMode');
        if (!$mode) {
            PHPJet::$app->tool->JSONOutput->setStatusFalse();
            PHPJet::$app->tool->JSONOutput->setMessageBoxText('No data to set');

            $this->modelAdmin->recordActions('Panel Mode', false, 'attempt to change mode failed - no data');
            return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
        }

        $result = $this->modelAdmin->setPanelState($mode);
        if (!$result) {
            PHPJet::$app->tool->JSONOutput->setStatusFalse();
            PHPJet::$app->tool->JSONOutput->setMessageBoxText('Unable to set Panel Mode');

            $this->modelAdmin->recordActions('Panel Mode', false, 'attempt to change mode failed - function returned false');
            return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
        }

        // everything is fine
        PHPJet::$app->tool->JSONOutput->setStatusTrue();
        PHPJet::$app->tool->JSONOutput->setMessageBoxText('Panel Mode successfully set');
        PHPJet::$app->tool->JSONOutput->setData([
            'panelMode' => $mode
        ]);

        $this->modelAdmin->recordActions('Panel Mode', true, 'attempt to change mode succeeded');
        return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
    }

    /**
     * @return string
     */
    public function actionGetMode(): string
    {
        $mode = $this->modelAdmin->getPanelState();
        if (!$mode) {
            PHPJet::$app->tool->JSONOutput->setStatusFalse();
            return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
        }

        PHPJet::$app->tool->JSONOutput->setStatusTrue();
        PHPJet::$app->tool->JSONOutput->setData([
            'panelMode' => $mode
        ]);
        return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
    }

    /**
     * @return string
     */
    public function actionSetDefaultWindow(): string
    {
        $defaultWindow = PHPJet::$app->system->request->getPOST('defaultWindow');
        if (!is_int($defaultWindow)) {
            PHPJet::$app->tool->JSONOutput->setStatusFalse();
            PHPJet::$app->tool->JSONOutput->setMessageBoxText('No data to set');

            $this->modelAdmin->recordActions('Panel Mode', false, 'attempt to set default window failed - no data');
            return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
        }

        $result = $this->modelAdmin->setDefaultWindow($defaultWindow);
        if (!$result) {
            PHPJet::$app->tool->JSONOutput->setStatusFalse();
            PHPJet::$app->tool->JSONOutput->setMessageBoxText('Unable to set default window');

            $this->modelAdmin->recordActions('Panel Mode', false, 'attempt to set default window failed - function returned false');
            return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
        }

        // everything is fine
        PHPJet::$app->tool->JSONOutput->setStatusTrue();
        PHPJet::$app->tool->JSONOutput->setMessageBoxText('Default window was set');
        PHPJet::$app->tool->JSONOutput->setData([
            'defaultWindow' => $defaultWindow
        ]);

        $this->modelAdmin->recordActions('Panel Mode', true, 'attempt to set default window succeeded');
        return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
    }

    /**
     * @return BadQueryStringException
     */
    public function actionGetDefaultWindow(): string
    {
        $defaultWindow = $this->modelAdmin->getDefaultWindow();
        if ($defaultWindow < 0) {
            PHPJet::$app->tool->JSONOutput->setStatusFalse();
            return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
        }

        PHPJet::$app->tool->JSONOutput->setStatusTrue();
        PHPJet::$app->tool->JSONOutput->setData([
            'defaultWindow' => $defaultWindow
        ]);
        return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
    }
}