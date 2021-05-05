<?php


namespace Jet\App\Database;


use Jet\App\Engine\ActiveRecord\Table;

/**
 * Class Tracker_Authority
 * @package Jet\App\Database
 * @deprecated
 */
class Tracker_Authority extends Table
{
    /**
     * @var bool
     */
    protected $_ignore = true;
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $authority_id;
    /**
     * @var string
     */
    public $action;
    /**
     * @var bool
     */
    public $status;
    /**
     * @var string
     */
    public $details;
    /**
     * @var string
     */
    public $url;
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