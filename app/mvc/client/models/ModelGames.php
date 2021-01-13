<?php


namespace Jet\App\MVC\Client\Models;


use Jet\App\Engine\Core\Model;

/**
 * Class ModelGames
 * @package Jet\App\MVC\Client\Models
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