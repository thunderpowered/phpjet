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
     * @param bool $checkToken
     * @param bool $auth
     */
    public function setAction(string $controllerName, string $url, string $actionName, array $params = [], bool $checkToken = true, bool $auth = true): void
    {
        $this->urls[$controllerName]['actions'][$actionName] = [
            'url' => $url,
            'params' => $params,
            'token' => $checkToken,
            'auth' => $auth
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