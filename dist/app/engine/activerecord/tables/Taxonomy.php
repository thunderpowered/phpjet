<?php


namespace Jet\App\Engine\ActiveRecord\Tables;


use Jet\App\Engine\ActiveRecord\ActiveRecord;

/**
 * Class Taxonomy
 * @package Jet\App\Engine\ActiveRecord\Tables
 */
class Taxonomy extends ActiveRecord
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $categories_id;
    /**
     * @var int
     */
    public $items_id;
    /**
     * @var string
     */
    protected $_primaryKey = 'id';
}