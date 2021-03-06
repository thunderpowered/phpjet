<?php


namespace Jet\App\Engine\ActiveRecord\Tables;


use Jet\App\Engine\ActiveRecord\ActiveRecord;

/**
 * Class Mods
 * @package Jet\App\Engine\ActiveRecord\Tables
 * @deprecated
 */
class Mods extends ActiveRecord
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $games_id;
    /**
     * @var int
     */
    public $users_id;
    /**
     * @var string
     */
    public $url;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $description;
    /**
     * @var string
     */
    public $file;
    /**
     * @var string
     */
    public $since;
    /**
     * @var string
     */
    protected $_primaryKey = 'id';
}