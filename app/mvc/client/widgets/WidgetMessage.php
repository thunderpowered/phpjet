<?php

namespace CloudStore\App\MVC\Client\Widgets;

use CloudStore\App\Engine\Core\Widget;

/**
 * Class WidgetMessage
 * @package CloudStore\App\MVC\Client\Widgets
 */
class WidgetMessage extends Widget
{
    /**
     * @var array
     */
    protected $staticFiles = [
        'js' => [
            'jquery.cookie',
            'widget_message'
        ],
        'css' => [
            'widget_message'
        ]
    ];

    /**
     * WidgetMessage constructor.
     * @param Widget $widget
     */
    public function __construct(Widget $widget)
    {
        parent::__construct($widget);
    }

    /**
     * @return string
     */
    public function getWidget(): string
    {
        return $this->render("widget_message", array());
    }
}
