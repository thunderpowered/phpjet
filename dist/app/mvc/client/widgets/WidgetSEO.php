<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 2018-07-23
 * Time: 12:53
 */

namespace Jet\App\MVC\Client\Widgets;

use Jet\PHPJet;
use Jet\App\Engine\Core\Widget;

class WidgetSEO extends Widget
{

    /**
     * @var array
     */
    private $codeFields = [
        "google_analytics",
        "google_searchconsole",
        "yandex_webmaster",
        "yandex_metrika"
    ];

    /**
     * @return string
     */
    public function getAnalytics(): string
    {
        $codes = $this->getCodes();
        return $this->render("widget_seo_analytics", [
            "codes" => $codes,
            "codeFields" => $this->codeFields
        ]);
    }

    /**
     * @return array
     */
    private function getCodes(): array
    {
        $codes = [];
        foreach ($this->codeFields as $name) {
            $codes[$name] = PHPJet::$app->system->settings->getContext($name);
        }
        return $codes;
    }
}