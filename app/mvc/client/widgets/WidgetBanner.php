<?php


namespace CloudStore\App\MVC\Client\Widgets;


use CloudStore\App\Engine\Core\Widget;
use CloudStore\CloudStore;

/**
 * Class WidgetBanner
 * @package CloudStore\App\Engine\Components
 */
class WidgetBanner extends Widget
{
    /**
     * @var string
     */
    private $banner;

    /**
     * WidgetBanner constructor.
     * @param Widget|null $widget
     */
    public function __construct(Widget $widget = null)
    {
        parent::__construct($widget);
        $this->loadBanner();
    }

    /**
     * @return string
     */
    public function getBanner(): string
    {
        return $this->banner;
    }

    private function loadBanner(): void
    {
        $this->banner = CloudStore::$app->system->settings->getContext('banner');
        $this->banner = CloudStore::$app->tool->utils->getImageLink($this->banner);
    }
}