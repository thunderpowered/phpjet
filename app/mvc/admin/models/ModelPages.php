<?php


namespace Jet\App\MVC\Admin\Models;


use Jet\App\Engine\ActiveRecord\Tables\PageBuilder;
use Jet\App\Engine\Core\Model;
use Jet\PHPJet;

/**
 * Class ModelPages
 * @package Jet\App\MVC\Admin\Models
 */
class ModelPages extends Model
{
    /**
     * @return array
     */
    public function loadPages(): array
    {
        $pages = PageBuilder::get(['type' => 'page']);
        foreach ($pages as $key => $page) {
            $page->since = PHPJet::$app->tool->formatter->formatDateString($page->since);
        }
        return $pages;
    }

    /**
     * @param int $pageID
     * @return PageBuilder|bool
     */
    public function loadPage(int $pageID)
    {
        $page = PageBuilder::getOne(['id' => $pageID, 'type' => 'page'], [], [], false);
        if (!$page) {
            return false;
        }

        $page->content = json_decode($page->content, true);
        $page->since = PHPJet::$app->tool->formatter->formatDateString($page->since);
        return $page;
    }
}