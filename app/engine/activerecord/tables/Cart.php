<?php

namespace CloudStore\App\Engine\ActiveRecord\Tables;


use CloudStore\App\Engine\ActiveRecord\ActiveRecord;

class Cart extends ActiveRecord
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
     * @var int
     */
    public $products_id;
    /**
     * @var int
     */
    public $products_modifications_id;
    /**
     * @var float
     */
    public $price;
    /**
     * @var int
     */
    public $amount;
    /**
     * @var int
     */
    public $ip;

    /**
     * @var string
     */
    protected $_primaryKey = 'id';
}