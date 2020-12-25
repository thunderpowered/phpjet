<?php


namespace CloudStore\App\MVC\Client\Models;


use CloudStore\App\Engine\Core\Model;

/**
 * Class ModelGames
 * @package CloudStore\App\MVC\Client\Models
 */
class ModelGames extends Model
{
    /**
     * ModelGames constructor.
     * @param string $name
     */
    public function __construct(string $name = "")
    {
        parent::__construct($name);
    }
}