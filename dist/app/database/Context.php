<?php


namespace Jet\App\Database;

use Jet\App\Engine\ActiveRecord\Table;

/**
 * Class Context
 * @package Jet\App\Database
 */
class Context extends Table
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $value;
}