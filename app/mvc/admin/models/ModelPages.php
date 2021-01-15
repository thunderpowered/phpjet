<?php


namespace Jet\App\MVC\Admin\Models;


use Jet\App\Engine\ActiveRecord\Tables\PageBuilder;
use Jet\App\Engine\Core\Model;
use Jet\App\Engine\Tools\ModelResponse;
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

    /**
     * @param array $page
     * @return ModelResponse
     */
    public function savePage(array $page): ModelResponse
    {
        if (!isset($page['content']) || !isset($page['url']) || !isset($page['title'])) {
            return $this->sendResponseToController(false, 'Request does not contain required fields');
        }

        $url = PHPJet::$app->tool->formatter->anyStringToURLString($page['url']);
        $title = PHPJet::$app->tool->formatter->anyStringToSearchString($page['title']);
        $jsonContent = json_encode($page['content']);
        if (!$jsonContent) {
            return $this->sendResponseToController(false, 'Unable to serialize page structure');
        }

        if (!isset($page['id'])) {
            // todo create page
        }

        $pageBuilder = PageBuilder::getOne(['id' => $page['id']]);
        if (!$pageBuilder) {
            return $this->sendResponseToController(false, 'Page does not exist');
        }

        $pageBuilder->content = $jsonContent;
        $pageBuilder->title = $title;
        $pageBuilder->url = $url;

        $result = $pageBuilder->save();
        if (!$result) {
            $this->sendResponseToController(false, 'Unable to create new page');
        }

        return $this->sendResponseToController(true, 'Page successfully updated');
    }

}