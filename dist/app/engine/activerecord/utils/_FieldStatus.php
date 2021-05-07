<?php


namespace Jet\app\engine\activerecord\utils;

/**
 * Class _FieldStatus
 * @package Jet\app\engine\activerecord\utils
 */
class _FieldStatus
{
    /**
     * @var bool
     */
    public $exists;
    /**
     * @var bool
     */
    public $type;
    /**
     * @var bool
     */
    public $NULL;
    /**
     * @var bool
     */
    public $index;
    /**
     * @var bool
     */
//    public $indexKey;
    /**
     * @var bool
     */
    public $indexType;
    /**
     * @var bool
     */
    public $indexUnique;
    /**
     * @var
     */
    public $indexPrimary;
    /**
     * @var bool
     */
    public $foreignKey;
    /**
     * @var bool
     */
    public $foreignKeyTable;
    /**
     * @var bool
     */
    public $foreignKeyField;
    /**
     * @var bool
     */
}