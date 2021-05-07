<?php


namespace Jet\App\Database;


use Jet\App\Engine\ActiveRecord\Table;

/**
 * Class Taxonomy
 * @package Jet\App\Database
 */
class Taxonomy extends Table
{
    /**
     * @var bool
     */
    public $_ignore = true;
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $category_id;
    /**
     * @var int
     */
    public $post_id;
}