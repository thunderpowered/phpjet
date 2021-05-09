<?php


namespace Jet\App\Engine\Components\ActiveRecord;

/**
 * Class _FieldIndex
 * @package Jet\App\Engine\Components\ActiveRecord
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
     * @var bool
     */
    public $foreignKey;
    /**
     * @var _FieldType
     */
    public $foreignKeyField;
    /**
     * @var string
     */
    public $foreignKeyType;
}