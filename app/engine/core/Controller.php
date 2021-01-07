<?php

namespace CloudStore\App\Engine\Core;

use CloudStore\App;
use CloudStore\App\Engine\Config\Config;
use CloudStore\CloudStore;

/**
 *
 * Main handler of Controller in MVC structure.
 * I'll try to reduce it. You don't need to use it.
 *
 */

/**
 * Class Controller
 * @package CloudStore\App\Engine\Core
 */
class Controller
{
    /**
     * @var App\App
     */
    private $app;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var View
     */
    protected $view;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var null|string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $noIndex;

    /**
     * @var array
     */
    protected $SEO;
    /**
     * @var array
     */
    protected $methods = [
        'POST', 'GET'
    ];

    /**
     * Controller constructor.
     * @param string $name
     * @param bool $enableTracker
     */
    public function __construct(string $name = "", bool $enableTracker = false)
    {
        $this->name = $name;
        $this->title = Config::$page['default_page_title'];

        global $app;
        $this->app = $app;

        if ($enableTracker) {
            CloudStore::$app->system->tracker->manageTable();
            CloudStore::$app->system->tracker->trackEverythingYouFind();
        }

        $this->checkQueryMethod();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param View $view
     */
    public function setView(View $view)
    {
        $this->view = $view;
    }

    /**
     * @param Model $model
     */
    public function setModel(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return array
     */
    public function SEO(): array
    {
        if (!$this->SEO['description']) {
            return [];
        }

        return [
            'name' => [
                'description' => $this->SEO['description'],
                'twitter:description' => $this->SEO['description']
            ],
            'property' => [
                'og:description' => $this->SEO['description']
            ]
        ];
    }

    /**
     * @return string
     */
    public function getRobots(): string
    {
        if ($this->noIndex) {

            return '<meta name="robots" content="noindex, nofollow"/> ';
        }
        return "";
    }

    /**
     * Returns page title
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getURLCanonical(): string
    {
        return '<link rel="canonical" href="' . CloudStore::$app->router->getURL() . '">';
    }

    private function checkQueryMethod()
    {
        $method = CloudStore::$app->system->request->getSERVER('REQUEST_METHOD');
        if (!in_array($method, $this->methods)) {
            CloudStore::$app->exit("Only " . implode('/', $this->methods) .  " allowed.");
        }

        // POST queries should contain some data, at least only CSRF-token
        if ($method === 'POST' && !CloudStore::$app->system->request->checkCSRFToken()) {
            CloudStore::$app->exit('Empty POST-queries are not allowed.');
        }
    }

    /**
     * @return mixed
     */
    public function getPagination()
    {
        return $this->model->getPagination();
    }
    public function __clone()
    {

    }
    public function __wakeup()
    {

    }
}
