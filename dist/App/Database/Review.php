<?php


namespace Jet\App\Database;


use Jet\App\Engine\ActiveRecord\Field;
use Jet\App\Engine\ActiveRecord\Table;

/**
 * Class Reviews
 * @package Jet\App\Database
 */
class Review extends Table
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $user_id;
    /**
     * @var int
     */
    public $post_id;
    /**
     * @var int
     */
    public $rating;
    /**
     * @var string
     */
    public $review;
    /**
     * @var string
     */
    public $datetime;

    public function __construct(bool $loaded = false)
    {
        parent::__construct($loaded);
        $this->id = Field::int()->setPrimary();
        $this->user_id = Field::int()->setIndex()->setForeignKey((new User())->id);
    }
}