<?php


namespace Jet\App\Database;


use Jet\App\Engine\ActiveRecord\Field;
use Jet\App\Engine\ActiveRecord\Table;

/**
 * Class Users
 * @package Jet\App\Database
 */
class User extends Table
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
     * @var string
     */
    protected $bio;
    /**
     * @var int
     */
    protected $age;
    /**
     * @var bool
     */
    protected $validated;

    /**
     * User constructor.
     * @param bool $loaded
     */
    public function __construct(bool $loaded = false)
    {
        parent::__construct($loaded);
        $this->id = Field::int()->setPrimary();
        $this->username = Field::varchar();
        $this->email = Field::varchar();
        $this->password = Field::varchar();
        $this->bio = Field::text();
        $this->age = Field::int();
        $this->validated = Field::bool();
    }
}