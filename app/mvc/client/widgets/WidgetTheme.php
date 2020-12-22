<?php

namespace CloudStore\App\MVC\Client\Widgets;

use CloudStore\App\Engine\Core\Widget;
use CloudStore\CloudStore;

/**
 * Class WidgetTheme
 * @package CloudStore\App\MVC\Client\Widgets
 */
class WidgetTheme extends Widget
{
    /**
     * @var string
     */
    public $style;

    /**
     * WidgetTheme constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->style = '';
        $this->loadTheme();
    }

    /**
     * @return string
     */
    public function getWidget(): string
    {
        return $this->style;
    }

    private function loadTheme(): void
    {

        $theme = CloudStore::$app->system->settings->getContext('theme');
        if (!$theme) {
            return;
        }

        $theme = unserialize($theme);

        //Header background
        if ($theme['theme__background']) {
            $this->style .= ".site-header{background-color:{$theme['theme__background']} !important} #StickyBar{background-color: {$theme['theme__background']} !important} #StickNavWrapper nav{background-color:{$theme['theme__background']} !important} ";
        }

        //Menu background
        if ($theme['theme__background_menu']) {
            $this->style .= ".main__column-left{background-color:{$theme['theme__background_menu']} !important} .main__main_menu-sub, .site_nav__item-block {background-color:{$theme['theme__background_menu']} !important} ";
        }

        //Menu text
        if ($theme['theme__text_color']) {
            $this->style .= ".main__main_menu-sub-item-list-title .link_span, .main__main_menu-sub-item-list-title a, .main__main_menu-sub-item-list-item .link_span, .main__main_menu-sub-item-list-item a {color:{$theme['theme__text_color']} !important} ";
        }

        //Menu link color
        if ($theme['theme__link_color']) {
            $this->style .= ".main__main_menu-sub-item-list-title a:hover .link_span, .main__main_menu-sub-item-list-item a:hover .link_span {color: {$theme['theme__link_color']} !important} ";
        }

        //Menu subtext color
        if ($theme['theme__subtext_color']) {
            $this->style .= ".main__main_menu-sub-item-list-title .menu__description, .main__main_menu-sub-item-list-item .menu__description{color: {$theme['theme__subtext_color']} !important} ";
        }

        //Menu count color
        if ($theme['theme__count_color']) {
            $this->style .= ".main__main_menu-sub-item-list-item .num_indicator{color: {$theme['theme__count_color']} !important} ";
        }

        //Page background color
        if ($theme['theme__page_background']) {
            $this->style .= "#PageContainer, .main-content{background-color:{$theme['theme__page_background']} !important} ";
        }

        //Page item hover
        if ($theme['theme__item_hover']) {
            $this->style .= ".product-card__overlay{background-color:{$theme['theme__item_hover']} !important} ";
        }

        //Page heading text
        if ($theme['theme__heading_text']) {
            $this->style .= "h1, h2, h3, h4, h5, h6 {color: {$theme['theme__heading_text']} !important} ";
        }

        //Body text
        if ($theme['theme__body']) {
            $this->style .= ".article{color: {$theme['theme__body']} !important} ";
        }

        //Button
        if ($theme['theme__button']) {
            $this->style .= ".btn, .btn--secondary, .rte .btn--secondary, .rte .btn, .rte .btn--secondary {background-color: {$theme['theme__button']} !important;} ";
        }

        if ($theme['theme__text_button_color']) {
            $this->style .= ".btn, .btn--secondary, .rte .btn--secondary, .rte .btn, .rte .btn--secondary {color: {$theme['theme__text_button_color']} !important} ";
        }

        //Button hover
        if ($theme['theme__button-hover']) {
            $this->style .= ".btn:hover, .btn--secondary:hover, .rte .btn:hover, .rte .btn--secondary:hover {background-color: {$theme['theme__button-hover']} !important}";
        }

        //Text links
        if ($theme['theme__color_link']) {
            $this->style .= ".article a:hover, .textForAffixMenu a, .textForAffixMenu a:hover, .section-header__subtext a, .section-header__subtext a:hover {border-bottom: 1px solid {$theme['theme__color_link']} !important} .article a:hover, .textForAffixMenu a:hover, .section-header__subtext a:hover { color: {$theme['theme__color_link']} !important; } ";
        }

        //Footer background
        if ($theme['theme__background_footer']) {
            $this->style .= ".site-footer {background-color: {$theme['theme__background_footer']} !important} ";
        }

        //Footer text
        if ($theme['theme__text_color-footer']) {
            $this->style .= ".site-footer h4{color: {$theme['theme__text_color-footer']} !important} ";
        }

        //Footer links
        if ($theme['theme__link_color-footer']) {
            $this->style .= ".site-footer a{color: {$theme['theme__link_color-footer']} !important} ";
        }
    }
}
