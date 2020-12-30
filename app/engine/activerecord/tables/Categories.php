<?php


namespace CloudStore\App\Engine\ActiveRecord\Tables;


use CloudStore\App\Engine\ActiveRecord\ActiveRecord;

/**
 * Class Categories
 * @package CloudStore\App\Engine\ActiveRecord\Tables
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