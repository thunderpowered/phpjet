<?php


namespace Jet\App\MVC\Client\Controllers;


use Jet\App\Engine\Core\Controller;
use Jet\App\MVC\Client\Models\ModelSearch;
use Jet\PHPJet;

/**
 * Class ControllerSearch
 * @package Jet\App\MVC\Client\Controllers
 */
class ControllerSearch extends Controller
{
    /**
     * @var ModelSearch
     */
    private $modelSearch;

    /**
     * ControllerSearch constructor.
     * @param string $name
     * @param bool $enableTracker
     */
    public function __construct(string $name = "", bool $enableTracker = false)
    {
        parent::__construct($name, $enableTracker);
        $this->modelSearch = new ModelSearch();
    }

    /**
     * @return string
     */
    public function actionBasic(): string
    {
        // action for main search page
        return $this->view->render('view_search_basic', [

        ]);
    }

    /**
     * @return string
     */
    public function actionAJAXQuickSearch(): string
    {
        $json = PHPJet::$app->system->request->getJSON();
        if (!$json) {
            return $this->view->returnJsonOutput(false);
        }

        $searchValue = $json['searchValue'];
        if (!$searchValue) {
            return $this->view->returnJsonOutput(false);
        }

        $searchResult = $this->modelSearch->quickSearch($searchValue, 20);

        return $this->view->returnJsonOutput(true, [
            'searchValue' => $searchValue,
            'searchResult' => $searchResult
        ]);
    }
}