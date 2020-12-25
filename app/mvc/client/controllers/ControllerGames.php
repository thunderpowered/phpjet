<?php


namespace CloudStore\App\MVC\Client\Controllers;

use CloudStore\App\Engine\Core\Controller;

/**
 * Class ControllerGames
 * @package CloudStore\App\MVC\Client\Controllers
 */
class ControllerGames extends Controller
{
    /**
     * ControllerGames constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return string
     */
    public function actionBasic(): string
    {
        return $this->view->render('view_games_basic', [

        ]);
    }
}