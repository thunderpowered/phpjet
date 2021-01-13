<?php


namespace Jet\App\MVC\Admin\Controllers;


use Jet\App\Engine\ActiveRecord\Tables\PageBuilder;
use Jet\App\Engine\Core\Controller;
use Jet\App\MVC\Admin\Models\ModelAdmin;
use Jet\App\MVC\Admin\Models\ModelPages;
use Jet\PHPJet;

/**
 * Class ControllerPages
 * @package Jet\App\MVC\Admin\Controllers
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
     * @var bool
     */
    protected $tokenRequired = true;
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
     * @param bool $enableTracker ]
     */
    public function __construct(string $name = "", bool $enableTracker = false)
    {
        parent::__construct($name, $enableTracker);
        $this->modelAdmin = new ModelAdmin();
        $this->modelPages = new ModelPages();

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
    public function actionLoadPages(): string
    {
        $pages = $this->modelPages->loadPages();
        // even if no pages -> return empty array

        PHPJet::$app->tool->JSONOutput->setStatusTrue();
        PHPJet::$app->tool->JSONOutput->setData([
            'pages' => $pages
        ]);
        return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
    }

    /**
     * @return string
     */
    public function actionLoadPage(): string
    {
        $pageID = PHPJet::$app->system->request->getJSON('page_id');
        if (!$pageID) {
            PHPJet::$app->tool->JSONOutput->setStatusFalse();
            PHPJet::$app->tool->JSONOutput->setMessageBoxText('Field "page_id" cannot be empty');
            return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
        }

        $page = $this->modelPages->loadPage($pageID);
        if (!$page) {
            PHPJet::$app->tool->JSONOutput->setStatusFalse();
            PHPJet::$app->tool->JSONOutput->setMessageBoxText('Page not found');
            return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
        }

        // and also since we are using page builder, load all the data we need
        PHPJet::$app->tool->JSONOutput->setStatusTrue();
        PHPJet::$app->tool->JSONOutput->setData([
            'page' => $page
        ]);
        return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
    }

    /**
     * @return string
     */
    public function actionLoadPageBuilder(): string
    {
        $pageBuilderData = PHPJet::$app->pageBuilder->getAllWorkspaceData();
        PHPJet::$app->tool->JSONOutput->setStatusTrue();
        PHPJet::$app->tool->JSONOutput->setData([
            'pageBuilder' => $pageBuilderData
        ]);
        return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
    }
}