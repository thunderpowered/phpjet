<?php


namespace CloudStore\App\MVC\Admin\Controllers;


use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\MVC\Admin\Models\ModelAdmin;
use CloudStore\CloudStore;

/**
 * Class ControllerStatistics
 * @package CloudStore\App\MVC\Admin\Controllers
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
    public function actionGetAdminActions(): string
    {
        $adminActions = $this->modelAdmin->getAdminActions();
        CloudStore::$app->tool->JSONOutput->setStatusTrue();
        CloudStore::$app->tool->JSONOutput->setData([
            'rows' => $adminActions
        ]);
        return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
    }
}