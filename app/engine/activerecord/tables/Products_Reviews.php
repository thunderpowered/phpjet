<?php


namespace CloudStore\App\Engine\ActiveRecord\Tables;

use CloudStore\App\Engine\ActiveRecord\ActiveRecord;

/**
 * Class Products_Reviews
 * @package CloudStore\App\Engine\ActiveRecord\Tables
 */
class Products_Reviews extends ActiveRecord
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
    public $products_id;
    /**
     * @var int
     */
    public $rating;
    /**
     * @var string
     */
    public $ip;
    /**
     * @var string
     */
    protected $_primaryKey = 'id';
}