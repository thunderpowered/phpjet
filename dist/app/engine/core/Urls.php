<?php


namespace Jet\App\Engine\Core;

/**
 * Class Urls
 * @package Jet\App\Engine\Core
 */
class Urls
{
    /**
     * @var array
     */
    protected $urls;

    /**
     * @param string $url
     * @param string $controllerName
     * @param array $params
     */
    public function setController(string $url, string $controllerName, array $params = []): void
    {
        $this->urls[$controllerName] = [
            'url' => $url,
            'params' => $params,
            'actions' => []
        ];
    }

    /**
     * @param string $controllerName
     * @param string $url
     * @param string $actionName
     * @param array $params
     */
    public function setAction(string $controllerName, string $url, string $actionName, array $params = []): void
    {
        $this->urls[$controllerName]['actions'][$actionName] = [
            'url' => $url,
            'params' => $params
        ];
    }

    /**
     * @return array
     */
    public function getUrls(): array
    {
        return $this->urls; // or maybe do not return and proceed is somehow internally?
    }
}