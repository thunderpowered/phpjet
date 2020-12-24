<?php


namespace CloudStore\App\MVC\Client\Controllers;


use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\MVC\Client\Models\ModelSearch;
use CloudStore\CloudStore;

/**
 * Class ControllerSearch
 * @package CloudStore\App\MVC\Client\Controllers
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
     */
    public function __construct(string $name = "")
    {
        parent::__construct($name);
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
        $json = CloudStore::$app->system->request->getJSON();
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