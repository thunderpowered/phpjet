<?php


namespace Jet\App\Engine\Components\ActiveRecord\Tables;

use Jet\App\Engine\Components\ActiveRecord\Field;
use Jet\App\Engine\Components\ActiveRecord\Table;

/**
 * Class Admin
 * @package Jet\App\Engine\Components\ActiveRecord\Tables
 */
class Admin extends Table
{
    /**
     * @var Field
     */
    protected $id;
    /**
     * @var Field
     */
    protected $username;
    /**
     * @var Field
     */
    protected $email;
    /**
     * @var Field
     */
    protected $password;
    /**
     * @var Field
     */
    protected $two_factor_auth;
    /**
     * @var Field
     * @deprecated
     */
    protected $session_token;
    /**
     * @var Field
     */
    protected $last_login;

    /**
     * Admin constructor.
     * @param bool $loaded
     */
    public function __construct(bool $loaded = false)
    {
        parent::__construct($loaded);
        $this->id = Field::int()->setPrimary();
        $this->username = Field::varchar()->setIndex(true);
        $this->email = Field::varchar()->setIndex(true);
        $this->password = Field::varchar();
        $this->two_factor_auth = Field::bool();
        $this->session_token = Field::varchar();
        $this->last_login = Field::dateTime();
    }
}