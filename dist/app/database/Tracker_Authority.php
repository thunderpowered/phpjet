<?php


namespace Jet\App\Database;


use Jet\App\Engine\Core\ActiveRecord;

/**
 * Class Tracker_Authority
 * @package Jet\App\Database
 */
class Tracker_Authority extends ActiveRecord
{
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
    /**
     * @var string
     */
    protected $_primaryKey = 'id';
}