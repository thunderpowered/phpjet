<?php


namespace CloudStore\App\Engine\Core;

use CloudStore\App\Engine\Tools\Cache;
use CloudStore\App\Engine\Tools\JSONOutput;
use CloudStore\App\Engine\Tools\SEO;
use CloudStore\App\Engine\Tools\Formatter;
use CloudStore\App\Engine\Tools\Utils;

/**
 * Class Tool
 * @package CloudStore\App\Engine\Core
 * @description Tool is the class that contains other classes from directory "Tools"
 * @property Utils $utils
 * @property SEO $SEO
 * @property Formatter $formatter
 * @property Cache $cache
 * @property JSONOutput $JSONOutput
 */
class Tool
{
    /**
     * @var Utils
     */
    private $utils;
    /**
     * @var SEO
     */
    private $SEO;
    /**
     * @var Formatter
     */
    private $formatter;
    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var JSONOutput
     */
    private $JSONOutput;

    /**
     * Tool constructor.
     */
    public function __construct()
    {
        // todo i still don't think it's good idea to make it with autoload
        // todo find solution for IDE to show methods for private members
    }

    /**
     * @param string $propertyName
     * @return mixed
     */
    public function __get(string $propertyName)
    {
        // it'd better to use empty(), but i assume that nobody will try to get non-existent property
        if ($this->$propertyName) {
            return $this->$propertyName;
        }

        $Class = NAMESPACE_ROOT . "\App\Engine\Tools\\" . ucfirst($propertyName);
        $this->$propertyName = new $Class();
        return $this->$propertyName;
    }
}