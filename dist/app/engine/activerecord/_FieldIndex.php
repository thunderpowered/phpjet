<?php


namespace Jet\App\Engine\ActiveRecord;

/**
 * Class _FieldIndex
 * @package Jet\App\Engine\ActiveRecord
 */
class _FieldIndex
{
    /**
     * @var string
     */
    public $index;
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