<?php


namespace CloudStore\App\Engine\ActiveRecord\Tables;


use CloudStore\App\Engine\ActiveRecord\ActiveRecord;

/**
 * Class Filters
 * @package CloudStore\App\Engine\ActiveRecord\Tables
 */
class Filters extends ActiveRecord
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $store;
    /**
     * @var int
     */
    public $category_id;
    /**
     * @var int
     */
    public $products_id;
    /**
     * @var string
     */
    public $attribute;
    /**
     * @var string
     */
    public $value;
    /**
     * @var int
     */
    public $order;
    /**
     * @var int
     */
    public $type;
    /**
     * @var bool
     */
    public $active;
    /**
     * @var bool
     */
    public $container;
    /**
     * @var bool
     */
    public $favorite;
    /**
     * @var string
     */
    protected $_primaryKey = 'id';
}