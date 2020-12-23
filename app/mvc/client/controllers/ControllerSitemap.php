<?php

namespace CloudStore\App\Engine\Controllers;

use CloudStore\App\Engine\Components\XMLMap;
use CloudStore\App\Engine\Components\XMLProducts;
use CloudStore\App\Engine\Components\YMLMap;
use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\Engine\Core\Router;
use CloudStore\App\Engine\Core\System;

/**
 * Class ControllerSitemap
 * @package CloudStore\App\Engine\Controllers
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