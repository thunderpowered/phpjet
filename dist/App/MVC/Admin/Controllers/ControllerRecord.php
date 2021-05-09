<?php

namespace Jet\App\MVC\Admin\Controllers;

use Jet\App\Engine\Core\Controller;
use Jet\App\Engine\Core\View;
use Jet\App\Engine\Interfaces\MessageBox;
use Jet\App\Engine\Interfaces\ViewResponse;
use Jet\App\MVC\Admin\Models\ModelAdmin;
use Jet\App\MVC\Admin\Models\ModelRecord;
use Jet\PHPJet;

/**
 * Class ControllerRecord
 */
class ControllerRecord extends Controller
{
    /**
     * @var ModelAdmin
     */
    private $modelAdmin;
    private $modelRecord;
    /**
     * ControllerRecord constructor.
     * @param View $view
     * @param bool $enableTracker
     */
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

        $this->modelRecord = new ModelRecord();
    }

    /**
     * @param string $method
     * @param array $ARGS
     * @param array $GET
     * @return ViewResponse
     */
    public function actionRecord(string $method, array $ARGS, array $GET): ViewResponse
    {
        if ($method === 'GET') {
            $result = $this->modelRecord->getRecord($ARGS['RECORD_ID'], $GET['mode']);
            if (!$result->status) {
                return $this->view->json(HTTP_NOT_FOUND, [], '', new MessageBox(MessageBox::ERROR, 'No records found'));
            } else {
                return $this->view->json(HTTP_OK, ['records' => $result->customData['records']]);
            }
        } else {
            return $this->view->json(HTTP_INTERNAL_SERVER_ERROR, [], '', new MessageBox(MessageBox::ERROR, 'todo'));
        }
    }
}