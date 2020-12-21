<?php

namespace CloudStore\App\MVC\Client\Widgets;

use CloudStore\App\Engine\Core\Widget;
use CloudStore\CloudStore;

/**
 * Class WidgetSorting
 * @package CloudStore\App\MVC\Client\Widgets
 */
class WidgetSorting extends Widget
{
    /**
     * @var string
     */
    private $url;
    /**
     * @var string
     */
    private $delimiter;
    /**
     * @var array
     */
    private $sortingInformation;
    /**
     * @var array
     */
    private $sortingRules;

    public function __construct(Widget $widget = null)
    {
        parent::__construct($widget);
    }

    /**
     * @return mixed|string
     */
    public function getWidget(): string
    {
        $this->prepareSortingInformation();

        return $this->render("widget_sorting", [
            "url" => $this->url,
            'sortingRules' => $this->sortingRules,
            'sortingInformation' => $this->sortingInformation
        ]);
    }

    private function prepareSortingInformation()
    {
        $this->sortingInformation = CloudStore::$app->tool->paginator->getSortingInformation();
        $this->url = CloudStore::$app->tool->paginator->getURL();
        $this->sortingRules = CloudStore::$app->tool->paginator->getSortingRules();
        $page = CloudStore::$app->system->request->getGET('page');

        if (array_key_exists('__default', $this->sortingRules)) {
            unset ($this->sortingRules['__default']);
        }

        foreach ($this->sortingRules as $key => $rule) {
            // for selected option
            if ($key === $this->sortingInformation['url']) {
                $this->sortingRules[$key]['current'] = true;
            }
            // set url for each option
            $this->sortingRules[$key]['url'] = http_build_query(['page' => $page, 'sort' => $key]);
            $this->sortingRules[$key]['url'] = $this->url . '?' . $this->sortingRules[$key]['url'];
        }
    }
}
