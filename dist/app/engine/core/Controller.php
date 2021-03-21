<?php

namespace Jet\App\Engine\Core;

use Jet\App;
use Jet\App\Engine\Config\Config;
use Jet\PHPJet;

/**
 *
 * Main handler of Controller in MVC structure.
 * I'll try to reduce it. You don't need to use it.
 *
 */

/**
 * Class Controller
 * @package Jet\App\Engine\Core
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
     * @deprecated
     */
    protected $methods = [
        'POST', 'GET'
    ];
    /**
     * @var bool
     */
    protected $tokenRequired = false;
    /**
     * @var string
     */
    protected $urlTokenSessionKey = 'url_token';
    /**
     * @var string
     */
    protected $urlTokenURLKey = 'token';
    /**
     * @var array
     */
    protected $routingRules = [];

    /**
     * Controller constructor.
     * @param string $name deprecated
     * @param bool $enableTracker
     */
    public function __construct(string $name = "", bool $enableTracker = false)
    {
        $this->name = get_class($this);
//        $this->name = $name;
        $this->title = Config::$page['default_page_title'];

        global $app;
        $this->app = $app;
        if ($enableTracker) {
            // todo
//            PHPJet::$app->system->tracker->manageTable();
//            PHPJet::$app->system->tracker->trackEverythingYouFind();
        }
    }

    /**
     * @param bool $onlyClass
     * @return string
     */
    public function getName(bool $onlyClass = true): string
    {
        if ($onlyClass) {
            $name = explode("\\", $this->name);
            return end($name);
        } else {
            return $this->name;
        }
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
     * @deprecated
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
     * @deprecated
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
     * @deprecated
     */
    public function getURLCanonical(): string
    {
        return '<link rel="canonical" href="' . PHPJet::$app->router->getURL() . '">';
    }

    /**
     * @return bool
     */
    public function isTokenRequired(): bool
    {
        return $this->tokenRequired;
    }

    /**
     * @return string
     */
    public function getURLTokenURLKey(): string
    {
        return $this->urlTokenURLKey;
    }

    public function getURLTokenSessionKey(): string
    {
        return $this->urlTokenSessionKey;
    }

    /**
     * @return array
     */
    public function getSupportedQueryMethods(): array
    {
        return $this->methods;
    }

    public function __clone()
    {

    }

    public function __wakeup()
    {

    }
}
