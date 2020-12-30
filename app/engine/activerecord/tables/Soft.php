<?php


namespace CloudStore\App\Engine\ActiveRecord\Tables;


use CloudStore\App\Engine\ActiveRecord\ActiveRecord;

/**
 * Class Games
 * @package CloudStore\App\Engine\ActiveRecord\Tables
 */
class Soft extends ActiveRecord
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
    public $icon;
    /**
     * @var string
     */
    public $cover;
    /**
     * @var string
     */
    public $description;
    /**
     * @var string
     */
    public $since;
    /**
     * @var string
     */
    protected $_primaryKey = 'id';
}