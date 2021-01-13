<?php


namespace Jet\App\MVC\Client\Widgets;


use Jet\App\Engine\Core\Widget;
use Jet\PHPJet;

/**
 * Class WidgetBanner
 * @package Jet\App\Engine\Components
 */
class WidgetBanner extends Widget
{
    /**
     * @var string
     */
    private $banner;
    /**
     * @var bool
     */
    private $bannerState;

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

    public function getWidget(): string
    {
        if (!$this->bannerState) {
            return '<!-- BANNER IS DISABLED -->';
        }

        return $this->render('widget_banner', [
            'bannerImageURL' => $this->banner
        ]);
    }

    private function loadBanner(): void
    {
        $this->bannerState = PHPJet::$app->system->settings->getContext('banner_state');

        if ($this->bannerState) {
            $this->banner = PHPJet::$app->system->settings->getContext('banner');
            $this->banner = PHPJet::$app->tool->utils->getImageLink($this->banner);
        }
    }
}