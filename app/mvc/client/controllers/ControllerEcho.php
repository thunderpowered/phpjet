<?php

namespace CloudStore\App\Engine\Controllers;

use CloudStore\App\Engine\Core\Controller;
use CloudStore\CloudStore;

/**
 * Class ControllerEcho
 * @package CloudStore\App\Engine\Controllers
 * @deprecated
 * It's just temporary solution that i don't need anymore
 * @todo remove Echo Controller and move static files to common directory
 */
class ControllerEcho extends Controller
{

    public function type()
    {
        return "act";
    }

    public function actionJs()
    {
        $js = CloudStore::$app->router->getLastRoutePart();
        $filename = ENGINE . 'static/js/' . $js . '.js';

        if (file_exists($filename)) {

            if (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Type: application/javascript');
            header('Content-Length: ' . filesize($filename));

            readfile($filename);
            exit;
        }
    }

    public function actionCss()
    {
        $css = CloudStore::$app->router->getLastRoutePart();
        $filename = ENGINE . 'static/css/' . $css . '.css';

        if (file_exists($filename)) {

            if (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Type: text/css');
            header('Content-Length: ' . filesize($filename));

            readfile($filename);
            exit;
        }
    }
}
