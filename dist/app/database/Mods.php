<?php


namespace Jet\App\Database;


use Jet\App\Engine\Core\ActiveRecord;

/**
 * Class Mods
 * @package Jet\App\Database
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