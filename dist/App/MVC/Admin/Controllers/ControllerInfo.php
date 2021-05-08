<?php


namespace Jet\App\MVC\Admin\Controllers;


use Jet\App\Engine\Core\Controller;
use Jet\App\MVC\Admin\Models\ModelAdmin;
use Jet\PHPJet;

/**
 * Class ControllerInfo
 * @package Jet\App\MVC\Admin\Controllers
 */
class ControllerInfo extends Controller
{
    /**
     * @var ModelAdmin
     */
    private $modelAdmin;
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
     * ControllerInfo constructor.
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
}