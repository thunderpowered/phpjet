<?php


namespace Jet\App\Engine\ActiveRecord\Tables;


use Jet\App\Engine\ActiveRecord\ActiveRecord;

/**
 * Class Items
 * @package Jet\App\Engine\ActiveRecord\Tables
 */
class Items extends ActiveRecord
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $parent;
    /**
     * @var int
     */
    public $users_id;
    /**
     * @var int
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