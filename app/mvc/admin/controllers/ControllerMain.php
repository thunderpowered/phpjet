<?php

namespace CloudStore\App\MVC\Admin\Controllers;

use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\MVC\Admin\Models\ModelAdmin;

/**
 * Class ControllerMain
 * @package CloudStore\App\MVC\Admin\Controllers
 */
class ControllerMain extends Controller
{
    /**
     * @var ModelAdmin
     */
    private $modelAdmin;
    /**
     * ControllerMain constructor.
     * @param string $name
     */
    public function __construct(string $name = "")
    {
        parent::__construct($name);
        $this->modelAdmin = new ModelAdmin();
    }

    /**
     * @return string
     */
    public function actionBasic(): string
    {
        $this->title = 'ModWare Admin Desktop';
        return $this->view->render('view_desktop', []);
    }
}