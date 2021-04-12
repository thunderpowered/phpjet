<?php


namespace Jet\App\Engine\Core;

use Jet\App\Engine\Tools\Cache;
use Jet\App\Engine\Tools\FileManager;
use Jet\App\Engine\Tools\JSONOutput;
use Jet\App\Engine\Tools\SEO;
use Jet\App\Engine\Tools\Formatter;
use Jet\App\Engine\Tools\Utils;
use Jet\App\Engine\Tools\Validator;

/**
 * Class Tool
 * @package Jet\App\Engine\Core
 * @description Tool is the class that contains other classes from directory "Tools"
 * @property Utils $utils
 * @property SEO $SEO
 * @property Formatter $formatter
 * @property Cache $cache
 * @property JSONOutput $JSONOutput
 * @property FileManager $fileManager
 * @property Validator $validator
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
     * @var FileManager
     */
    private $fileManager;
    /**
     * @var Validator
     */
    private $validator;

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