<?php


namespace CloudStore\App\Engine\ActiveRecord\Tables;

use CloudStore\App\Engine\ActiveRecord\ActiveRecord;

/**
 * Class Products_Likes
 * @package CloudStore\App\Engine\ActiveRecord\Tables
 */
class Products_Likes extends ActiveRecord
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
     * @var string
     */
    public $ip;
    /**
     * @var string
     */
    protected $_primaryKey = 'id';
}