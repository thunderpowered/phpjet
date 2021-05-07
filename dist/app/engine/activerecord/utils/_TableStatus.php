<?php


namespace Jet\App\Engine\ActiveRecord\Utils;

/**
 * Class _TableStatus
 * @package Jet\App\Engine\ActiveRecord\Utils
 */
class _TableStatus
{
    /**
     * @var bool
     */
    public $ignored;
    /**
     * @var bool
     */
    public $exists;
    /**
     * @var array
     */
    public $type;
    /**
     * @var array
     */
    public $fields;
    /**
     * @var int
     * @description 0 - ignored, 1 - does not exist, 2 - needs to be updated, 3 - up to date
     */
    public $status;
}