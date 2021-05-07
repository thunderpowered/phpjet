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
    public $index;
    /**
     * @var array
     */
    public $foreignKey;
    /**
     * @var array
     */
    public $fields;
}