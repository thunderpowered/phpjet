<?php


namespace Jet\App\Engine\ActiveRecord\Tables;

use Jet\App\Engine\ActiveRecord\ActiveRecord;

/**
 * Class Settings
 * @package Jet\App\Engine\ActiveRecord\Tables
 */
class Context extends ActiveRecord
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $value;
    /**
     * @var string
     */
    protected $_primaryKey = 'id';
}