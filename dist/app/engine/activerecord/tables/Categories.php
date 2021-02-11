<?php


namespace Jet\App\Engine\ActiveRecord\Tables;


use Jet\App\Engine\ActiveRecord\ActiveRecord;

/**
 * Class Categories
 * @package Jet\App\Engine\ActiveRecord\Tables
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