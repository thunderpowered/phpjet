<?php

namespace Jet\App\MVC\Client\Widgets;

use Jet\App\Engine\Core\Widget;
use Jet\PHPJet;

/**
 * Class WidgetLogotype
 * @package Jet\App\MVC\Client\Widgets
 */
class WidgetLogotype extends Widget
{
    /**
     * @var string
     */
    private $favicon;
    /**
     * @var string
     */
    private $logotype;

    /**
     * WidgetLogotype constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->loadLogotype();
        $this->loadFavicon();
    }

    /**
     * @return string
     */
    public function getFavicon()
    {
        return $this->favicon;
    }

    /**
     * @return string
     */
    public function getLogotype()
    {
        return $this->logotype;
    }

    public function _temp_pb__getLogotype()
    {
        return $this->render('_pb__widgetlogotype', [
            'logotype' => $this->logotype
        ]);
    }

    private function loadLogotype(): void
    {
        $this->favicon = PHPJet::$app->system->settings->getContext('favicon');
        $this->favicon = PHPJet::$app->tool->utils->getImageLink($this->favicon);
    }

    private function loadFavicon(): void
    {
        $this->logotype = PHPJet::$app->system->settings->getContext('logotype');
        $this->logotype = PHPJet::$app->tool->utils->getImageLink($this->logotype);
    }
}
