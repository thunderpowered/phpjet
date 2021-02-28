<?php


namespace Jet\App\Database;


use Jet\App\Engine\Core\ActiveRecord;

/**
 * Class Taxonomy
 * @package Jet\App\Database
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