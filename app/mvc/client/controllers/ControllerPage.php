<?php

namespace CloudStore\App\MVC\Client\Controllers;

use CloudStore\App\Engine\Core\Controller;
use CloudStore\CloudStore;

/**
 * Class ControllerPage
 * @package CloudStore\App\MVC\Client\Controllers
 */
class ControllerPage extends Controller
{
    /**
     * ControllerPage constructor.
     * @param string $name
     */
    public function __construct(string $name = "")
    {
        parent::__construct($name);
    }

    /**
     * @param string $url
     * @return string
     */
    public function actionBasic(): string
    {
        $url = CloudStore::$app->router->getURL(false);
        // check exceptions first (for best performance)
        $exception = CloudStore::$app->pageBuilder->getException($url);
        if ($exception) {
            return $this->executeAction($exception);
        }

        // ordinary way
        $pageData = CloudStore::$app->pageBuilder->getPageData($url);
        if (!$pageData) {
            return CloudStore::$app->router->errorPage404();
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

        return CloudStore::$app->router->errorPage404();
    }
}