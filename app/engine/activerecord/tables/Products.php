<?php

namespace CloudStore\App\Engine\ActiveRecord\Tables;

use CloudStore\App\Engine\ActiveRecord\ActiveRecord;

/**
 * Class Products
 * @package CloudStore\App\Engine\ActiveRecord
 */
class Products extends ActiveRecord
{
    /**
     * @var int
     */
    public $store;
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $url;
    /**
     * @var string
     */
    public $title;
    /**
     * @var float
     */
    public $price_old;
    /**
     * @var float
     */
    public $price;
    /**
     * @var string
     */
    public $brand;
    /**
     * @var string
     */
    public $brand_latin;
    /**
     * @var string
     */
    public $picture;
    /**
     * @var string
     */
    public $description;
    /**
     * @var string
     */
    public $message;
    /**
     * @var float
     */
    public $rating;
    /**
     * @var string
     */
    public $datetime;
    /**
     * @var int
     */
    public $pre;
    /**
     * @var int
     * available at the moment
     */
    public $quantity_available;
    /**
     * @var int
     * available in stock
     */
    public $quantity_stock;
    /**
     * @var string
     * available later
     */
    public $quantity_possible;
    /**
     * @var string
     * delivery date from store
     */
    public $delivery_available;
    /**
     * @var string
     * delivery date from stock
     */
    public $delivery_stock;
    /**
     * @var string
     * delivery date from supplier
     */
    public $delivery_possible;
    /**
     * @var float
     */
    public $weight;
    /**
     * @var float
     */
    public $weight_package;
    /**
     * @var string
     */
    public $sku;
    /**
     * @var int
     */
    public $category_id;
    /**
     * @var int
     */
    public $category_attributes;
    /**
     * @var int
     */
    public $visible;
    /**
     * @var int
     */
    public $visible_main;
    /**
     * @var int
     */
    public $show;
    /**
     * @var int
     */
    public $favorite;
    /**
     * @var int
     */
    public $views;
    /**
     * @var int
     */
    public $trash;
    /**
     * @var string
     */
    protected $_primaryKey = 'id';
}