<?php

namespace CloudStore\App\MVC\Admin\Widgets;

use CloudStore\App\Engine\Core\Widget;
use CloudStore\CloudStore;

class WidgetMisc extends Widget
{
    /**
     * @var string
     */
    private $contextKeyLogotype = 'logotype';

    /**
     * WidgetMisc constructor.
     * @param Widget|null $widget
     */
    public function __construct(Widget $widget = null)
    {
        parent::__construct($widget);
    }

    /**
     * @return string
     */
    public function getLogotype(): string
    {
        $logotype = CloudStore::$app->system->settings->getContext($this->contextKeyLogotype);
        if (!$logotype) {
            return '';
        }

        return CloudStore::$app->tool->utils->getImageLink($logotype);
    }
}