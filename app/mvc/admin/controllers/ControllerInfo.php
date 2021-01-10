<?php


namespace CloudStore\App\MVC\Admin\Controllers;


use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\MVC\Admin\Models\ModelAdmin;
use CloudStore\CloudStore;

/**
 * Class ControllerInfo
 * @package CloudStore\App\MVC\Admin\Controllers
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
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('Not authorized');
            $output = CloudStore::$app->tool->JSONOutput->returnJSONOutput();

            $this->modelAdmin->recordActions('Auth', false, 'Unauthorized query registered');
            // force application to send output and stop
            CloudStore::$app->router->immediateResponse($output);
        }
    }
}