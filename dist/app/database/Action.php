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
     * @var Field
     */
    protected $id;
    /**
     * @var Field
     */
    protected $method;
    /**
     * @var Field
     */
    protected $url;
    /**
     * @var Field
     */
    protected $type;
    /**
     * @var Field
     */
    protected $details;
    /**
     * @var Field
     */
    protected $referer;
    /**
     * @var Field
     */
    protected $ip;
    /**
     * @var Field
     */
    protected $user_agent;
    /**
     * @var Field
     */
    protected $post;

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