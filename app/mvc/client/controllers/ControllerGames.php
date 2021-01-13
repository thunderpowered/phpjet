<?php


namespace Jet\App\MVC\Client\Controllers;

use Jet\App\Engine\Core\Controller;

/**
 * Class ControllerGames
 * @package Jet\App\MVC\Client\Controllers
 */
class ControllerGames extends Controller
{
    public function __construct(string $name = "", bool $enableTracker = false)
    {
        parent::__construct($name, $enableTracker);
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