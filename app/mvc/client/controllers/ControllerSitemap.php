<?php

namespace Jet\App\Engine\Controllers;

use Jet\App\Engine\Components\XMLMap;
use Jet\App\Engine\Components\XMLProducts;
use Jet\App\Engine\Components\YMLMap;
use Jet\App\Engine\Core\Controller;
use Jet\App\Engine\Core\Router;
use Jet\App\Engine\Core\System;

/**
 * Class ControllerSitemap
 * @package Jet\App\Engine\Controllers
 * @deprecated
 */
class ControllerSitemap extends Controller
{

    public function actionBasic()
    {

        $sitemap = Router::getAction(false, false);

        if ($sitemap) {
            if (!strpos($sitemap, ".xml")) {

                Router::errorPage404();
            }

            XMLMap::showMap($sitemap);
        }

        $sitemap = $this->model->getSiteMap();
        return $this->view->render("view_sitemap", [
            "sitemap" => $sitemap
        ]);
    }

    public function actionYandex()
    {

        // @todo combine all get methods into one class

        $sitemap = Router::getLastRoutePart();

        if (!strpos($sitemap, ".yml")) {

            Router::errorPage404();
        }

        YMLMap::getYML($sitemap);
    }

    public function actionGoogle()
    {

        // @todo combine all get methods into one class

        $sitemap = Router::getLastRoutePart();

        if (!strpos($sitemap, ".xml")) {

            Router::errorPage404();
        }

        XMLProducts::getXML($sitemap);
    }
}