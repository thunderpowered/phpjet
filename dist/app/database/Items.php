<?php


namespace Jet\App\Database;


use Jet\App\Engine\Core\ActiveRecord;

/**
 * Class Items
 * @package Jet\App\Database
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