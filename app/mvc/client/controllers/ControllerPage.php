<?php

namespace Jet\App\MVC\Client\Controllers;

use Jet\App\Engine\Core\Controller;
use Jet\PHPJet;

/**
 * Class ControllerPage
 * @package Jet\App\MVC\Client\Controllers
 */
class ControllerPage extends Controller
{
    /**
     * ControllerPage constructor.
     * @param string $name
     */
    public function __construct(string $name = "")
    {
        parent::__construct($name, true);
    }

    /**
     * @return string
     */
    public function actionBasic(): string
    {
        $url = PHPJet::$app->router->getURL(false);
        // check exceptions first (for best performance)
        $exception = PHPJet::$app->pageBuilder->getException($url);
        if ($exception) {
            return $this->executeAction($exception);
        }

        // ordinary way
        $pageData = PHPJet::$app->pageBuilder->getPageData($url);
        if (!$pageData) {
            return PHPJet::$app->router->errorPage404();
        }

        // quick note
        // use router->errorPage404(true) inside of pageBuilder chunks to execute immediate show up of page 404 (or 500 using errorPage500)
        // because each chunk proceeds apart from others and ordinary call will lead to putting error message inside single block while others will proceed normally
        return $this->view->render('view_pageBuilder', [
            'page' => $pageData
        ]);
    }

    /**
     * @param array $classAndFunction
     * @return string
     */
    private function executeAction(array $classAndFunction): string
    {
        if (class_exists($classAndFunction['class']) && method_exists($classAndFunction['class'], $classAndFunction['function']) && is_callable([$classAndFunction['class'], $classAndFunction['function']])) {
            $object = new $classAndFunction['class']();
            return $object->$classAndFunction['function']();
        }

        return PHPJet::$app->router->errorPage404();
    }
}