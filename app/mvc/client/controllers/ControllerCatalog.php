<?php

namespace CloudStore\App\MVC\Client\Controllers;

use CloudStore\App\Engine\Config\Config;
use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\MVC\Client\Models\ModelCatalog;
use CloudStore\App\MVC\Client\Models\ModelProducts;
use CloudStore\CloudStore;

/**
 * Class ControllerCatalog
 * @package CloudStore\App\Engine\Controllers
 */
class ControllerCatalog extends Controller
{
    /**
     * @var ModelCatalog
     */
    private $modelCatalog;
    /**
     * @var ModelProducts
     */
    private $modelProducts;
    /**
     * @var array
     */
    private $category;
    /**
     * @var array
     */
    protected $SEO = [
        'meta_title' => '',
        'meta_description' => '',
        'category_name' => ''
    ];
    private $categoryNameDefault = 'Catalog';
    /**
     * ControllerCatalog constructor.
     * @param string $name
     */
    public function __construct(string $name = "")
    {
        parent::__construct($name);
        $this->modelProducts = new ModelProducts();
        $this->modelCatalog = new ModelCatalog();
    }

    /**
     * @return string
     */
    public function actionBasic()
    {
        // Getting parameter
        $url = CloudStore::$app->router->getAction();
        $this->category = $this->modelCatalog->getCategory($url);

        if(!$this->category) {
            return CloudStore::$app->router->errorPage404();
        }

        // @todo find all children categories
//        $categories = $this->modelCatalog->findAllChildCategories($this->category);

        $products = $this->modelProducts->load(['category' => $this->category['category_id']]);
        $productsAmount = $this->modelProducts->getAmountOfLastSearch();

        $this->SEO['category_name'] = $this->category['name'] ?? $this->categoryNameDefault;
        // todo: title is name of category now, should make it customizable in admin panel in the future
        $this->title = $this->SEO['category_name'];
        $this->SEO['meta_title'] = $this->category["category_meta_title"] ?? $this->title;
        $this->SEO['meta_description'] = $this->category['category_meta_description'] ?? $this->category['category_description'];

        //$prepared_filters = $this->model->preparedFilters($category_id, $category['category_handle']);
        $prepared_filters = null;

        return $this->view->render($this->view->getTemplateName(), [
            'products' => $products,
            'category' => $this->category,
            'category_name' => $this->category['name'],
            'category_id' => $this->category['category_id'],
            'category_desc' => $this->category['category_description'] ?? NULL,
            'category_handle' => $this->category['category_handle'] ?? NULL,
            'prepared_filters' => $prepared_filters,
            'products_count' => $productsAmount
        ]);
    }

    /**
     * @return array
     */
    public function SEO(): array
    {
        $description = preg_replace("|<h\d>(.+)</h\d>|isU", '', $this->SEO['meta_description']);
        $description = CloudStore::$app->tool->utils->removeSpecialChars($description);
        $url = CloudStore::$app->router->getURL();

        return [
            'property' => [
                'og:type' => 'category',
                'og:title' => $this->SEO['meta_title'],
                'og:description' => $description,
                'og:url' => $url,
                'og:site_name' => Config::$config['site_name']
            ],
            'name' => [
                'description' => $description,
                'twitter:site' => '@' . Config::$config['site_handler'],
                'twitter:card' => 'summary',
                'twitter:title' => $this->SEO['meta_title'],
                'twitter:description' => $description
            ]
        ];
    }

    /**
     * @return mixed|void
     */
    public function getPagination()
    {
        return $this->modelCatalog->getPagination();
    }
}
