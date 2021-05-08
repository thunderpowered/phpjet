<?php


namespace Jet\app\engine\activerecord\utils;

use Jet\App\Engine\ActiveRecord\Table;

/**
 * Class Builder
 * @package Jet\app\engine\activerecord\Utils
 */
class Builder
{
    /**
     * @var Table
     */
    private $table;

    /**
     * Builder constructor.
     * @param Table $table
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
    }
}