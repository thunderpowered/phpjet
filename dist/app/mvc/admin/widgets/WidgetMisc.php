<?php

namespace Jet\App\MVC\Admin\Widgets;

use Jet\App\Engine\Core\Widget;
use Jet\PHPJet;

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
        $logotype = PHPJet::$app->system->settings->getContext($this->contextKeyLogotype);
        if (!$logotype) {
            return '';
        }

        return PHPJet::$app->tool->utils->getImageLink($logotype);
    }
}