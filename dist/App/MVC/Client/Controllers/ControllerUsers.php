<?php


namespace Jet\App\MVC\Client\Controllers;


use Jet\App\Engine\Core\Controller;
use Jet\App\MVC\Client\Models\ModelUsers;
use Jet\PHPJet;

/**
 * Class ControllerUsers
 * @package Jet\App\MVC\Client\Controllers
 */
class ControllerUsers extends Controller
{
    private $modelUsers;
    /**
     * @var array
     */
    protected $routingRules = [
        '/users/{USER_ID}' => [
            'action' => 'actionGetUser'
        ]
    ];
    /**
     * ControllerUsers constructor.
     * @param string $name
     * @param bool $enableTracker
     */
    public function __construct(string $name = "", bool $enableTracker = false)
    {
        parent::__construct($name, $enableTracker);
        $this->modelUsers = new ModelUsers();
    }

    /**
     * @return string
     */
    public function actionBasic(): string
    {
        return '';
    }

    /**
     * @param int $USER_ID
     * @return string
     */
    public function actionGetUser(int $USER_ID): string
    {
        $user = $this->modelUsers->getUserByID($USER_ID);
        if (!$user) {
            return PHPJet::$app->router->errorPage404();
        }

        return $this->view->render('view_single_user', [
            'user' => $user
        ]);
    }
}