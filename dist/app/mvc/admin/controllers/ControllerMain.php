<?php

namespace Jet\App\MVC\Admin\Controllers;

use Jet\App\Engine\Core\Controller;
use Jet\App\Engine\Interfaces\ViewResponse;
use Jet\App\MVC\Admin\Models\ModelAdmin;
use Jet\PHPJet;

/**
 * Class ControllerMain
 * @package Jet\App\MVC\Admin\Controllers
 */
class ControllerMain extends Controller
{
    /**
     * @var array
     */
    protected $methods = [
        'GET'
    ];
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
        parent::__construct($name, true);
        $this->modelAdmin = new ModelAdmin();
    }

    /**
     * @return ViewResponse
     */
    public function actionBasicGET(): ViewResponse
    {
        $this->title = 'PHPJet Admin Desktop'; // todo include site name
        $initState = [
            '__api_base' => PHPJet::$app->router->getHost() . '/'
        ];
        return $this->view->html('view_desktop', [
            '__INITIAL_STATE__' => $initState
        ]);
    }
}