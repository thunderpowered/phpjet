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
        [
            'props' => [
                'id' => 'SampleWidget',
                'name' => 'Sample Widget',
                'jet' => [
                    'class' => 'SampleWidget',
                    'function' => 'getWidget'
                ]
            ],
            'params' => [

            ],
            'children' => []
        ],
        [
            'props' => [
                'id' => 'itemsGroupedByDate',
                'name' => 'Items Grouped By Date',
                'jet' => [
                    'class' => 'ControllerMain',
                    'function' => 'actionBasic'
                ]
            ],
            'params' => [
                'sort' => [
                    'what' => 'rating',
                    'how' => 'desc'
                ],
//                'group' => 'month', // todo add selector
                'limit' => 20,
                'single' => true
            ],
            'children' => []
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
     * @param bool $includeType
     * @return array
     */
    public function getChunks(bool $includeType = true): array
    {
        if (!$includeType) {
            return $this->chunks;
        }

        $chunks = [];
        // since PageBuilder has recursive architecture, each node MUST contain field 'type' just to determine what to do
        foreach ($this->chunks as $key => $chunk) {
            $chunk['type'] = 'chunk';
            $chunks[] = $chunk;
        }
        return $chunks;
    }

    /**
     * @param bool $includeType
     * @return array
     */
    public function getNodes(bool $includeType = true): array
    {
        // todo
        return [];
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