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
    private App\App $app;

    /**
     * @var Model
     */
    protected Model $model;

    /**
     * @var View
     */
    protected View $view;

    /**
     * @var string
     */
    protected string $title;

    /**
     * @var null|string
     */
    protected string $name;

    /**
     * @var bool
     */
    protected bool $noIndex;

    /**
     * @var array
     */
    protected array $SEO;

    /**
     * Controller constructor.
     * @param string $name
     */
    public function __construct(string $name = "")
    {
        $this->name = $name;
        $this->title = Config::$page['default_page_title'];

        global $app;
        $this->app = $app;
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
