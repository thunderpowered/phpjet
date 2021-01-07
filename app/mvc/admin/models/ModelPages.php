<?php


namespace CloudStore\App\MVC\Admin\Models;


use CloudStore\App\Engine\ActiveRecord\Tables\Pages;
use CloudStore\App\Engine\Core\Model;

/**
 * Class ModelPages
 * @package CloudStore\App\MVC\Admin\Models
 */
class ModelPages extends Model
{
    /**
     * @return array
     */
    public function loadPages(): array
    {
        $pages = Pages::get();

        // some preparations

        return $pages;
    }
}