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
    public $validated;

    /**
     * User constructor.
     * @param bool $loaded
     */
    public function __construct(bool $loaded = false)
    {
        parent::__construct($loaded);
        $this->id = Field::int();
        $this->username = Field::varchar();
        $this->email = Field::varchar();
        $this->password = Field::varchar();
        $this->bio = Field::text();
        $this->age = Field::int();
        $this->validated = Field::bool();
    }
}