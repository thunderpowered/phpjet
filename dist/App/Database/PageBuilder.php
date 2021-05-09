<?php


namespace Jet\App\Database;


use Jet\App\Engine\Components\ActiveRecord\Table;

/**
 * Class PageBuilder
 * @package Jet\App\Database
 * @deprecated (more into in main pb class)
 */
class PageBuilder extends Table
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
}