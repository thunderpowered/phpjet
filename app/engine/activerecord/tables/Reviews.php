<?php


namespace CloudStore\App\Engine\ActiveRecord\Tables;


use CloudStore\App\Engine\ActiveRecord\ActiveRecord;

/**
 * Class Reviews
 * @package CloudStore\App\Engine\ActiveRecord\Tables
 */
class Reviews extends ActiveRecord
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $users_id;
    /**
     * @var int
     */
    public $item_id;
    /**
     * @var string
     */
    public $item_table;
    /**
     * @var int
     */
    public $rating;
    /**
     * @var string
     */
    public $review;
    /**
     * @var string
     */
    public $datetime;
    /**
     * @var string
     */
    protected $_primaryKey = 'id';
}