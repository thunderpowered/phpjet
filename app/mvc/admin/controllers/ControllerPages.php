<?php


namespace CloudStore\App\MVC\Admin\Controllers;


use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\MVC\Admin\Models\ModelAdmin;
use CloudStore\App\MVC\Admin\Models\ModelPages;
use CloudStore\CloudStore;

/**
 * Class ControllerPages
 * @package CloudStore\App\MVC\Admin\Controllers
 */
class ControllerPages extends Controller
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
     * @var ModelPages
     */
    private $modelPages;
    /**
     * ControllerPages constructor.
     * @param string $name
     * @param bool $enableTracker]
     */
    public function __construct(string $name = "", bool $enableTracker = false)
    {
        parent::__construct($name, $enableTracker);
        $this->modelAdmin = new ModelAdmin();
        $this->modelPages = new ModelPages();

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
    public function actionLoadPages(): string
    {
        $pages = $this->modelPages->loadPages();
        // even if no pages -> return empty array

        CloudStore::$app->tool->JSONOutput->setStatusTrue();
        CloudStore::$app->tool->JSONOutput->setData([
            'pages' => $pages
        ]);
        return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
    }
}