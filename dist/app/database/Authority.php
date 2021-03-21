<?php


namespace Jet\App\Database;

use Jet\App\Engine\ActiveRecord\Table;

/**
 * Class Authority
 * @package Jet\App\Database
 * @deprecated
 */
class Authority extends Table
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
}