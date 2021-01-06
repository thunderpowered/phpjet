<?php


namespace CloudStore\App\MVC\Client\Controllers;

use CloudStore\App\Engine\Core\Controller;

/**
 * Class ControllerGames
 * @package CloudStore\App\MVC\Client\Controllers
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