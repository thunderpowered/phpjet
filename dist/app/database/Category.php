<?php


namespace Jet\App\Database;


use Jet\App\Engine\ActiveRecord\Field;
use Jet\App\Engine\ActiveRecord\Table;

/**
 * Class Categories
 * @package Jet\App\Database
 */
class Category extends Table
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $url;
    /**
     * @var string
     */
    protected $description;
    /**
     * @var string
     */
    protected $image;

    /**
     * Category constructor.
     * @param bool $loaded
     */
    public function __construct(bool $loaded = false)
    {
        parent::__construct($loaded);
        $this->id = Field::int()->setPrimary();
        $this->name = Field::varchar()->setIndex();
        $this->url = Field::varchar()->setIndex();
        $this->description = Field::text();
        $this->image = Field::varchar();
    }
}