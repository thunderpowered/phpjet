<?php


namespace CloudStore\App\MVC\Client\Models;


use CloudStore\App\Engine\Core\Model;

/**
 * Class ModelReviews
 * @package CloudStore\App\MVC\Client\Models
 */
class ModelReviews extends Model
{
    /**
     * ModelReviews constructor.
     * @param string $name
     */
    public function __construct(string $name = "")
    {
        parent::__construct($name);
    }

    /**
     * @param string $category
     * @param int $itemID
     */
    public function getRating(string $category, int $itemID)
    {

    }
}