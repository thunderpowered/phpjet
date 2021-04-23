<?php


namespace Jet\App\Database;


use Jet\App\Engine\ActiveRecord\_FieldType;
use Jet\App\Engine\ActiveRecord\Field;
use Jet\App\Engine\ActiveRecord\Table;

/**
 * Class Users
 * @package Jet\App\Database
 * @property Field|_FieldType id
 * @property Field username
 * @property Field email
 * @property Field password
 * @property Field bio
 * @property Field age
 * @property Field validated
 * @property Field datetime
 */
class User extends Table
{
    protected $id;
    protected $username;
    protected $email;
    protected $password;
    protected $bio;
    protected $age;
    protected $validated;
    protected $datetime;

    /**
     * User constructor.
     * @param bool $loaded
     */
    public function __construct(bool $loaded = false)
    {
        parent::__construct($loaded);
        $this->id = Field::int()->setPrimary();
        $this->username = Field::varchar()->setIndex();
        $this->email = Field::varchar()->setIndex();
        $this->password = Field::varchar();
        $this->bio = Field::text();
        $this->age = Field::int();
        $this->validated = Field::bool();
        $this->datetime = Field::dateTime();
    }
}