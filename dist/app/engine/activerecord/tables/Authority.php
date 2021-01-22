<?php


namespace Jet\App\Engine\ActiveRecord\Tables;

use Jet\App\Engine\ActiveRecord\ActiveRecord;

/**
 * Class Authority
 * @package Jet\App\Engine\ActiveRecord\Tables
 */
class Authority extends ActiveRecord
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
     * @var bool
     */
    public $two_factor_auth;
    /**
     * @var string
     */
    public $session_token;
    /**
     * @var string
     */
    public $last_login;
    /**
     * @var string
     */
    protected $_primaryKey = 'id';
}