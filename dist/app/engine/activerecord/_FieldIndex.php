<?php


namespace Jet\App\Engine\ActiveRecord;

/**
 * Class _FieldIndex
 * @package Jet\App\Engine\ActiveRecord
 */
class _FieldIndex
{
    /**
     * @var bool
     */
    public $index;
    /**
     * @var string
     */
    public $key;
    /**
     * @var string
     */
    public $type;
    /**
     * @var bool
     */
    public $unique;
    /**
     * @var bool
     */
    public $primary;
    /**
     * @var Field
     */
    public $foreignKeyField;
    /**
     * @var string
     */
    public $foreignKeyType;
}