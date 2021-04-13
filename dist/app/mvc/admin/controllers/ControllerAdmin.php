<?php


namespace Jet\App\MVC\Admin\Controllers;

use Jet\App\Engine\Core\Controller;
use Jet\App\Engine\Interfaces\MessageBox;
use Jet\App\Engine\Interfaces\ViewResponse;
use Jet\App\MVC\Admin\Models\ModelAdmin;
use Jet\PHPJet;

/**
 * Class ControllerAdmin
 * @package Jet\App\MVC\Admin\Controllers
 */
class ControllerAdmin extends Controller
{
    /**
     * @var ModelAdmin
     */
    protected $modelAdmin;

    /**
     * ControllerAdmin constructor.
     * @param string $name
     * @param bool $enableTracker
     */
    public function __construct(string $name = "", bool $enableTracker = false)
    {
        parent::__construct($name, $enableTracker);
        $this->modelAdmin = new ModelAdmin();

        if (!$this->modelAdmin->isAdminAuthorized()) {
            PHPJet::$app->router->immediateResponse(
                $this->view->json(HTTP_UNAUTHORIZED, [], '', new MessageBox(MessageBox::ERROR, 'Not authorized'))
            );
        }
    }

    /**
     * @param string $method
     * @return ViewResponse
     */
    public function actionSettingsAppearance(string $method): ViewResponse
    {
        // todo
        return new ViewResponse();
    }

    /**
     * @param string $method
     * @return ViewResponse
     */
    public function actionSettingsMode(string $method): ViewResponse
    {

    }

    /**
     * @return string
     */
    protected function returnUnauthorized(): string
    {
        PHPJet::$app->tool->JSONOutput->setStatusFalse();
        PHPJet::$app->tool->JSONOutput->setMessageBoxText('Not authorized');
        $this->modelAdmin->recordActions('Auth', false, 'Unauthorized query registered');
        return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
    }

    /**
     * @param array $result
     * @return string
     */
    protected function returnSuccessfulAuthorizationMessage(array $result): string
    {
        PHPJet::$app->tool->JSONOutput->setStatusTrue();
        PHPJet::$app->tool->JSONOutput->setMessageBoxText('Successfully authorized.');
        PHPJet::$app->tool->JSONOutput->setAction('S');
        PHPJet::$app->tool->JSONOutput->setData([
            'urls' => $result['urls'],
            'auth' => true
        ]);
        return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
    }
}