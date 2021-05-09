<?php


namespace Jet\App\Database;

use Jet\App\Engine\Components\ActiveRecord\Field;
use Jet\App\Engine\Components\ActiveRecord\Table;

/**
 * Class Context
 * @package Jet\App\Database
 * @property Field id
 * @property Field name
 * @property Field value
 */
class Context extends Table
{
    protected $id;
    protected $name;
    protected $value;
    /**
     * Context constructor.
     * @param bool $loaded
     */
    public function __construct(bool $loaded = false)
    {
        parent::__construct($loaded);
        $this->id = Field::int()->setPrimary();
        $this->name = Field::varchar();
        $this->value = Field::text();
    }
}