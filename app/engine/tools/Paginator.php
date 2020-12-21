<?php

namespace CloudStore\App\Engine\Tools;

use CloudStore\App\Engine\Core\Component;
use CloudStore\App\Engine\Core\Router;
use CloudStore\CloudStore;

/**
 *
 * Component: ShopEngine Pagination
 * Description: ShopEngine automatically paginate items. It works if \CloudStore\App\Engine\Tools\Getter::getProducts and \CloudStore\App\Engine\Tools\Getter::getDataWithPagination in use.
 *
 * Instructions:
 * You need to point number of items per page (see Getter), then you need to put next constructions in your Controller and Model:
 *
 * Controller:
 *
 * public function getPagination()
 * {
 * return $this->model->getPagination();
 * }
 *
 * then Model:
 *
 * public function getPagination()
 * {
 * $main = "/".ShopEngine::getRoute()[1]."/".ShopEngine::getAction().'?';
 * return ShopEngine::Help()->getPagination($main);
 * }
 *
 * As you can see, you can customize it.
 * Finally put next construction in you template part (where you want to see pagination-widget):
 *
 * <?= $this->controller->getPagination()?>
 *
 */
// TODO: move all pagination features from System to component

/**
 * Class Pagination
 * @package CloudStore\App\Engine\Tools
 * @deprecated
 */
class Paginator
{
    /**
     * @var string
     */
    protected $sortBy;
    /**
     * @var array
     */
    protected $limit;
    /**
     * @var array
     */
    protected $orderBy;
    /**
     * @var int
     */
    protected $total;
    /**
     * @var int
     */
    protected $pageNumber;
    /**
     * @var string
     */
    private $URI;
    /**
     * @var array
     */
    private $sortingInformation;
    /**
     * @var array
     */
    private $sortingRules;
    /**
     * @var
     */
    private $url;

    /**
     * Paginator constructor.
     */
    public function __construct()
    {
        $this->URI = CloudStore::$app->router->getRequestURI();
        $this->URI = '/' . substr($this->URI, 1);
        $this->sortingInformation = [];

        $this->sortingRules = [];
        $this->sortingInformation = [];

        $url = CloudStore::$app->router->getURL();
        $this->prepareURL($url);
    }

    /**
     * @param string $url
     * @return mixed
     */
    public function getPagination(string $url)
    {
        return false;
        // Bad idea to use different component
        // TODO: remove it
        $count = ProductManager::$count;
        $num = ProductManager::$num;

        $array = $this->preparePagination($count, $num);
        $page = $array["page"];
        $sorting = $array["sortBy"];
        $total = $array["total"];
        $main_page = Paginator::prepareURL($url);

        // TODO: GET BY VIEW!!!
        // UPD. I think it is good idea to make it with Widget.
        if (file_exists(HOME . 'widgets/views/' . THEME_VIEWS . "pagination.php")) {
            @include HOME . 'widgets/views/' . THEME_VIEWS . "pagination.php";
        } else {
            @include ENGINE . 'widgets/views/pagination.php';
        }
    }

    /**
     * @param int $productsAmount
     * @param int $itemsPerPage
     * @return array
     */
    public function preparePagination(int $productsAmount, int $itemsPerPage = 20, array $sortingRules = []): array
    {
        $total = 0;
        $limit = [];

        $this->sortingRules = $sortingRules;
        $sortingInformation = $this->prepareSortingInformation($sortingRules);
        $pageNumber = CloudStore::$app->system->request->getGET('page');
        if (!$pageNumber || $pageNumber < 1) {
            $pageNumber = 1;
        }

        if ($productsAmount > 0) {
            $total = (($productsAmount - 1) / $itemsPerPage) + 1;
            $total = intval($total);

            if ($pageNumber > $total) {
                $pageNumber = $total;
            }

            $start = $pageNumber * $itemsPerPage - $itemsPerPage;
            $limit = [
                $start, $itemsPerPage
            ];
        }

        return [
            "display_name" => $sortingInformation['display_name'],
            "limit" => $limit,
            "page" => $pageNumber,
            "total" => $total,
            "db_order_by" => $sortingInformation['db_order_by']
        ];
    }

    /**
     * @return array
     */
    public function getSortingInformation(): array
    {
        return $this->sortingInformation;
    }

    /**
     * @return string
     */
    public function getURL(): string
    {
        return $this->url;
    }

    /**
     * @return array
     */
    public function getSortingRules(): array
    {
        return $this->sortingRules;
    }

    /**
     * @param array $sortingRules
     * @return array
     */
    private function prepareSortingInformation(array $sortingRules = []): array
    {
        $sortBy = CloudStore::$app->system->request->getGET('sort');
        if (!$sortBy || !array_key_exists($sortBy, $sortingRules)) {
            // the default value should be set explicitly
            if (array_key_exists('__default', $sortingRules)) {
                $this->sortingInformation['url'] = '__default';
                $this->sortingInformation = $sortingRules[$this->sortingInformation['url']];
            } else {
                // return empty value if nothing else left
                $this->sortingInformation = [
                    'db_order_by' => [],
                    'display_name' => '',
                    'url' => ''
                ];
            }
        } else {
            $this->sortingInformation = $sortingRules[$sortBy];
            $this->sortingInformation['url'] = $sortBy;
        }

        return $this->sortingInformation;
    }

    // Uses only for dynamic URL (with GETs)

    /**
     * @param string $url
     * @return string
     * @description returns url string without 'page' and 'sort' variables in it
     */
    private function prepareURL(string $url): string
    {
        $this->url = CloudStore::$app->tool->utils->removeGETVariableFromURL($url, 'page');
        $this->url = CloudStore::$app->tool->utils->removeGETVariableFromURL($this->url, 'sort');
        return $this->url;
    }
}
