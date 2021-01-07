<?php


namespace CloudStore\App\Engine\ActiveRecord\Tables;


use CloudStore\App\Engine\ActiveRecord\ActiveRecord;

/**
 * Class Tracker_Authority
 * @package CloudStore\App\Engine\ActiveRecord\Tables
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