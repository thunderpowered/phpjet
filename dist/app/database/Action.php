<?php


namespace Jet\App\Database;


use Jet\App\Engine\ActiveRecord\Table;

/**
 * Class Tracker
 * @package Jet\App\Database
 */
class Action extends Table
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $method;
    /**
     * @var string
     */
    public $url;
    /**
     * @var
     */
    public $type;
    /**
     * @var
     */
    public $details;
    /**
     * @var string
     */
    public $referer;
    /**
     * @var string
     */
    public $ip;
    /**
     * @var string
     */
    public $user_agent;
    /**
     * @var string
     */
    public $datetime;
    /**
     * @var string
     */
    public $post;
}