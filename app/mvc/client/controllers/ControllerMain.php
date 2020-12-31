<?php

namespace CloudStore\App\MVC\Client\Controllers;

use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\MVC\Client\Models\ModelItems;
use CloudStore\CloudStore;

/**
 * Class ControllerMain
 * @package CloudStore\App\MVC\Client\Controllers
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

    public function __construct(string $name = "")
    {
        parent::__construct($name);

        // load from settings
        $this->SEO['description'] = CloudStore::$app->system->settings->getContext('site_description');
        $this->SEO['title'] = CloudStore::$app->system->settings->getContext('site_name');

        CloudStore::$app->system->tracker->trackEverythingYouFind();

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
