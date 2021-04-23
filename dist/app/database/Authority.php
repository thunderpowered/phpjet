<?php


namespace Jet\App\Database;

use Jet\App\Engine\ActiveRecord\Field;
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
    protected $id;
    /**
     * @var string
     */
    protected $username;
    /**
     * @var string
     */
    protected $email;
    /**
     * @var string
     */
    protected $password;
    /**
     * @var bool
     */
    protected $two_factor_auth;
    /**
     * @var string
     */
    protected $session_token;
    /**
     * @var string
     */
    protected $last_login;

    /**
     * Authority constructor.
     * @param bool $loaded
     */
    public function __construct(bool $loaded = false)
    {
        parent::__construct($loaded);
        $this->id = Field::int()->setPrimary();
        $this->username = Field::varchar()->setIndex();
        $this->email = Field::varchar()->setIndex();
        $this->password = Field::varchar();
        $this->two_factor_auth = Field::bool();
        $this->session_token = Field::varchar();
        $this->last_login = Field::dateTime();
    }
}