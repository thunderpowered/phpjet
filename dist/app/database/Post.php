<?php


namespace Jet\App\Database;


use Jet\App\Engine\ActiveRecord\Table;

/**
 * Class Post
 * @package Jet\App\Database
 */
class Post extends Table
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $parent;
    /**
     * @var int
     */
    public $user_id;
    /**
     * @var int
     */
    public $name;
    /**
     * @var string
     */
    public $url;
    /**
     * @var string
     */
    public $icon;
    /**
     * @var string
     */
    public $cover;
    /**
     * @var string
     */
    public $description;
    /**
     * @var string
     */
    public $since;
}