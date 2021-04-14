<?php

namespace Jet\App\MVC\Admin\Controllers;

use Jet\App\Engine\Core\Controller;
use Jet\App\Engine\Core\View;
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
     * @param View $view
     * @param bool $enableTracker
     */
    public function __construct(View $view, bool $enableTracker = false)
    {
        parent::__construct($view, $enableTracker);
    }

    /**
     * @return ViewResponse
     */
    public function actionHome(): ViewResponse
    {
        $this->title = 'PHPJet Admin Desktop'; // todo include site name
        $initState = [
            '__api_base' => PHPJet::$app->router->getHost() . '/'
        ];
        return $this->view->html('desktop', [
            '__INIT_STATE__' => $initState
        ]);
    }
}