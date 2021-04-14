<?php


namespace Jet\App\MVC\Admin\Controllers;

use http\Message;
use Jet\App\Engine\Core\Controller;
use Jet\App\Engine\Core\View;
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

    public function __construct(View $view, bool $enableTracker = false)
    {
        parent::__construct($view, $enableTracker);
        $this->modelAdmin = new ModelAdmin();

        $admin = $this->modelAdmin->isAdminAuthorized();
        if (!$admin->status) {
            PHPJet::$app->router->immediateResponse(
                $this->view->json(HTTP_UNAUTHORIZED, [], '', new MessageBox(MessageBox::ERROR, 'Not authorized'))
            );
        }
    }

    /**
     * @param string $method
     * @param array $ARGS
     * @return ViewResponse
     */
    public function actionSettings(string $method, array $ARGS): ViewResponse
    {
        if (!$this->modelAdmin->validateAdmin($ARGS['ADMIN_ID'])) {
            return $this->view->json(HTTP_UNAUTHORIZED, [], '', new MessageBox(MessageBox::ERROR, "You don't have permissions for this action"));
        }

        if ($method === 'GET') {
            $settings = $this->modelAdmin->getAdminSettings($ARGS['ADMIN_ID'], $ARGS['SETTINGS']);
            if ($settings->status) {
                return $this->view->json(HTTP_OK, [$ARGS['SETTINGS'] => $settings->customData]);
            } else {
                return $this->view->json(HTTP_BAD_REQUEST, [], '', new MessageBox(MessageBox::ERROR, $settings->message));
            }
        } else {
            // todo
            exit('still not supported');
        }
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