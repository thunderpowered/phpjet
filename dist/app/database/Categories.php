<?php


namespace Jet\App\Database;


use Jet\App\Engine\Core\ActiveRecord;

/**
 * Class Categories
 * @package Jet\App\Database
 */
class Categories extends ActiveRecord
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
    public $url;
    /**
     * @var string
     */
    public $description;
    /**
     * @var string
     */
    public $cover;
    /**
     * @var string
     */
    protected $_primaryKey = 'id';
}