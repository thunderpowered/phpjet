<?php


namespace Jet\App\Database;

use Jet\App\Engine\Core\ActiveRecord;

/**
 * Class Context
 * @package Jet\App\Database
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