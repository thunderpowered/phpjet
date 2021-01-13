<?php

namespace Jet\App\MVC\Client\Controllers;

use Jet\App\Engine\Core\Controller;
use Jet\App\MVC\Client\Models\ModelItems;
use Jet\PHPJet;

/**
 * Class ControllerMain
 * @package Jet\App\MVC\Client\Controllers
 */
class ControllerMain extends Controller
{
    /**
     * @var ModelItems
     */
    private $modelItems;
    /**
     * @var array
     */
    protected $SEO = [
        'description' => '',
        'title' => ''
    ];

    /**
     * ControllerMain constructor.
     * @param string $name
     * @param bool $enableTracker
     */
    public function __construct(string $name = "", bool $enableTracker = false)
    {
        parent::__construct($name, $enableTracker);

        // load from settings
        $this->SEO['description'] = PHPJet::$app->system->settings->getContext('site_description');
        $this->SEO['title'] = PHPJet::$app->system->settings->getContext('site_name');

        $this->modelItems = new ModelItems();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function actionBasic()
    {
        if ($this->SEO['title']) {
            $this->title = $this->SEO['title'];
        }

        $bestLastItems = $this->modelItems->getItemsGroupedByDateWithASingleParent('month', 'rating', 'desc');

        return $this->view->render('view_main', [
            'bestLastItems' => $bestLastItems
        ]);
    }
}
