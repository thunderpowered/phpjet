<?php


namespace Jet\App\Engine\Core;

use Jet\PHPJet;

/**
 * Class PageBuilder
 * @package Jet\App\Engine\Core
 */
class PageBuilder
{
    /**
     * @var bool
     */
    private $active;
    /**
     * @var array
     */
    private $chunks = [
        // todo find the way to organize it better
        [
            //
            'id' => 'itemsGroupedByDate',
            'name' => 'Items Grouped By Date',
            'params' => [
                'sort' => [
                    'what' => 'rating',
                    'how' => 'desc'
                ],
                'group' => 'month',
                'limit' => 20,
                'single' => true
            ],
            'class' => 'ControllerMain',
            'function' => 'actionBasic'
        ]
    ];
    /**
     * @var array
     * url => [class, function]
     */
    private $exceptions = [
        'api/quickSearch' => [
            'class' => 'ControllerSearch',
            'function' => 'actionAJAXQuickSearch'
        ]
    ];
    /**
     * @var string
     */
    private $contextKeyPrefix = 'pagebuilder__';

    /**
     * @param string $url
     * @return bool|array
     */
    public function getException(string $url)
    {
        // todo add regexp to exception to make it more flexible
        return isset($this->exceptions[$url]) ? $this->exceptions[$url] : false;
    }

    /**
     * @param string $url
     * @return array
     */
    public function getPageData(string $url): array
    {
        // todo
        return [];
    }

    /**
     * @return array
     */
    public function getTemplates(): array
    {
        $templates = \Jet\App\Engine\ActiveRecord\Tables\PageBuilder::get(['type' => 'template'], [], [], false);
        if (!$templates) {
            return [];
        }

        foreach ($templates as $key => $template) {
            $template->content = json_decode($template->content, true);
        }
        return $templates;
    }

    /**
     * @param int $templateID
     * @param array $content
     * @return bool
     */
    public function saveTemplate(int $templateID = 0, array $content = []): bool
    {
        return false;
    }

    /**
     * @return array
     */
    public function getChunks(): array
    {
        return $this->chunks;
    }

    /**
     * @return array
     * Just a union of functions above
     */
    public function getAllWorkspaceData(): array
    {
        return [
            'templates' => $this->getTemplates(),
            'chunks' => $this->getChunks()
        ];
    }


    /**
     * @param string $contextName
     * @param bool $removeSpecialChars
     * @return string
     */
    private function loadPageBuilderContext(string $contextName, bool $removeSpecialChars = true): string
    {
        $contextKey = $this->contextKeyPrefix . $contextName;
        return PHPJet::$app->system->settings->getContext($contextKey, $removeSpecialChars);
    }
}