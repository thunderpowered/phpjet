<?php

namespace CloudStore\App\MVC\Client\Controllers;

use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\MVC\Client\Models\ModelMods;
use CloudStore\CloudStore;

/**
 * Class ControllerMain
 * @package CloudStore\App\MVC\Client\Controllers
 */
class ControllerMain extends Controller
{
    /**
     * @var ModelMods
     */
    private $modelMods;
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

        $this->modelMods = new ModelMods();
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

        $bestLastMods = $this->modelMods->getModsLastMonth();

        return $this->view->render('view_main', [
            'bestLastMods' => $bestLastMods
        ]);
    }
}
