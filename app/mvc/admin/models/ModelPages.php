<?php


namespace CloudStore\App\MVC\Admin\Models;


use CloudStore\App\Engine\ActiveRecord\Tables\Pages;
use CloudStore\App\Engine\Core\Model;
use CloudStore\CloudStore;

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
        foreach ($pages as $key => $page) {
            $page->since = CloudStore::$app->tool->formatter->formatDateString($page->since);
        }
        return $pages;
    }

    /**
     * @param int $pageID
     * @return Pages|bool
     */
    public function loadPage(int $pageID)
    {
        $page = Pages::getOne(['id' => $pageID], [], [], false);
        if (!$page) {
            return false;
        }

        $page->since = CloudStore::$app->tool->formatter->formatDateString($page->since);
        return $page;
    }
}