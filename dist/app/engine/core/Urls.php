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
     * @var string[]
     */
    protected $types = [
        'controller',
        'action'
    ];

    /**
     * @param string $url
     * @param string $type
     * @param string $classOrFunction
     * @return bool
     */
    public function setUrl(string $url, string $type, string $classOrFunction): bool
    {
        if (empty ($url) || empty ($type) || empty ($classOrFunction) || !in_array($type, $this->types)) {
            return false;
        }
        $this->urls[$url] = [
            'action' => $classOrFunction
        ];
        return true;
    }
}