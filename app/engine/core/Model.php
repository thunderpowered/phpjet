<?php

namespace CloudStore\App\Engine\Core;

/**
 *
 * Main handler of Model in MVC structure.
 * There is nothing to see.
 *
 */
/**
 * Class Model
 * @package CloudStore\App\Engine\Core
 */
class Model
{
    /**
     * @var string
     */
    private $name;

    /**
     * Model constructor.
     * @param string $name
     */
    public function __construct(string $name = "")
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @deprecated
     */
    public function getPagination()
    {

    }

    public function __clone()
    {

    }

    public function __wakeup()
    {

    }
}
