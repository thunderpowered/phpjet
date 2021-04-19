<?php


namespace Jet\App\Database;


use Jet\App\Engine\ActiveRecord\Field;
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
    public $post;

    /**
     * Action constructor.
     * @param bool $loaded
     */
    public function __construct(bool $loaded = false)
    {
        parent::__construct($loaded);
        $this->id = Field::int()->setPrimary();
        $this->method = Field::varchar();
        $this->url = Field::varchar();
        $this->type = Field::varchar();
        $this->details = Field::text();
        $this->referer = Field::varchar();
        $this->ip = Field::varchar();
        $this->user_agent = Field::varchar();
        $this->post = Field::text();
    }
}