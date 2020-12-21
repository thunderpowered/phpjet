<?php

namespace CloudStore\App\Engine\Controllers;

use CloudStore\App\Engine\Core\Controller;

/**
 * Class ControllerAjax
 * @package CloudStore\App\Engine\Controllers
 */
class ControllerAjax extends Controller
{
    /**
     * ControllerAjax constructor.
     * @param string $name
     */
    public function __construct(string $name = "")
    {
        parent::__construct($name);
    }
}
