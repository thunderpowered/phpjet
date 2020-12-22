<?php

namespace CloudStore\App\MVC\Client\Widgets;

use CloudStore\App\Engine\Core\Widget;
use CloudStore\CloudStore;

/**
 * Class WidgetLogotype
 * @package CloudStore\App\MVC\Client\Widgets
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

    private function loadLogotype(): void
    {
        $this->favicon = CloudStore::$app->system->settings->getContext('favicon');
        $this->favicon = CloudStore::$app->tool->utils->getImageLink($this->favicon);
    }

    private function loadFavicon(): void
    {
        $this->logotype = CloudStore::$app->system->settings->getContext('logotype');
        $this->logotype = CloudStore::$app->tool->utils->getImageLink($this->logotype);
    }
}
