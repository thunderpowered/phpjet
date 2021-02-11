<?php


namespace Jet\App\MVC\Client\Models;


use Jet\App\Engine\ActiveRecord\Tables\Users;
use Jet\App\Engine\Core\Model;

/**
 * Class ModelUsers
 * @package Jet\App\MVC\Client\Models
 */
class ModelUsers extends Model
{
    /**
     * @param int $userID
     * @return Users
     */
    public function getUserByID(int $userID): Users
    {
        return Users::getOne(['id' => $userID]);
    }
}