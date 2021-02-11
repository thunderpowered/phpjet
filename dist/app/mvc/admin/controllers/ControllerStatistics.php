<?php


namespace Jet\App\MVC\Admin\Controllers;


use Jet\App\Engine\Core\Controller;
use Jet\App\MVC\Admin\Models\ModelAdmin;
use Jet\PHPJet;

/**
 * Class ControllerStatistics
 * @package Jet\App\MVC\Admin\Controllers
 */
class ControllerStatistics extends Controller
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
     * ControllerStatistics constructor.
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
    public function actionGetAdminActions(): string
    {
        $adminActions = $this->modelAdmin->getAdminActions();
        PHPJet::$app->tool->JSONOutput->setStatusTrue();
        PHPJet::$app->tool->JSONOutput->setData([
            'rows' => $adminActions
        ]);
        return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
    }
}