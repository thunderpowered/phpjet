<?php


namespace CloudStore\App\Engine\ActiveRecord\Tables;

use CloudStore\App\Engine\ActiveRecord\ActiveRecord;

/**
 * Class Settings
 * @package CloudStore\App\Engine\ActiveRecord\Tables
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