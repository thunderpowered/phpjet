<?php


namespace Jet\App\Engine\ActiveRecord\Tables;


use Jet\App\Engine\ActiveRecord\ActiveRecord;

/**
 * Class Pages
 * @package Jet\App\Engine\ActiveRecord\Tables
 */
class PageBuilder extends ActiveRecord
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $url;
    /**
     * @var string
     */
    public $content;
    /**
     * @var string
     */
    public $comment;
    /**
     * @var string
     */
    public $since;
    /**
     * @var bool
     */
    public $cache;
    /**
     * @var string
     */
    protected $_primaryKey = 'id';
}