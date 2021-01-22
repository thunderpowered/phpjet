<?php


namespace Jet\App\MVC\Client\Models;


use Jet\App\Engine\Core\Model;

/**
 * Class ModelReviews
 * @package Jet\App\MVC\Client\Models
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