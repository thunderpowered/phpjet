<?php


namespace Jet\App\Database;


use Jet\App\Engine\Core\ActiveRecord;

/**
 * Class Users
 * @package Jet\App\Database
 */
class Users extends ActiveRecord
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $username;
    /**
     * @var string
     */
    public $email;
    /**
     * @var string
     */
    public $password;
    /**
     * @var string
     */
    public $bio;
    /**
     * @var int
     */
    public $age;
    /**
     * @var bool
     */
    public $active;
    /**
     * @var string
     */
    public $since;
    /**
     * @var string
     */
    protected $_primaryKey = 'id';
}