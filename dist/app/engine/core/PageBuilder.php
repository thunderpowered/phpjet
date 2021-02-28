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
        // todo organize it
        // todo and move somewhere else
        // since blocks could be implemented in MVC and we're in the Core folder
        // and maybe add to database or something
        // widgets
        [
            'props' => [
                'id' => 'WidgetLogotype',
                'name' => 'Logotype',
                'jet' => [
                    'class' => 'WidgetLogotype',
                    'function' => '_temp_pb__getLogotype'
                ]
            ],
            'params' => [

            ],
            'children' => []
        ],
        [
            'props' => [
                'id' => 'WidgetBanner',
                'name' => 'Banner',
                'jet' => [
                    'class' => 'WidgetBanner',
                    'function' => 'getWidget'
                ]
            ],
            'params' => [

            ],
            'children' => []
        ],
        [
            'props' => [
                'id' => 'Menu',
                'name' => 'Menu',
                'jet' => [
                    'class' => 'WidgetMenu',
                    'function' => 'getHeaderMenu'
                ]
            ],
            'params' => [

            ],
            'children' => []
        ],
        [
            'props' => [
                'id' => 'ItemRootList',
                'name' => 'Custom Menu 1',
                'jet' => [
                    'class' => 'WidgetMenu',
                    'function' => 'getItemRootList'
                ]
            ],
            'params' => [

            ],
            'children' => []
        ],
//        [
//            'props' => [
//                'id' => 'NewItems',
//                'name' => 'New Items',
//                'jet' => [
//                    'class' => 'WidgetHot',
//                    'function' => 'getNewItems'
//                ]
//            ],
//            'params' => [
//
//            ],
//            'children' => []
//        ],
        // controllers
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
     * @var string
     */
    private $templatePrefix = '_pb__';

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
     * @return \Jet\App\Engine\Core\Tables\PageBuilder|bool
     */
    public function getPageData(string $url)
    {
        return false;

        // todo prepare url
        return \Jet\App\Engine\Core\Tables\PageBuilder::getOne(['url' => $url, 'type' => 'page'], [], [], false);
    }

    /**
     * @return array
     */
    public function getTemplates(): array
    {
        $templates = \Jet\App\Engine\Core\Tables\PageBuilder::get(['type' => 'template'], [], [], false);
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
     * @param \Jet\App\Engine\Core\Tables\PageBuilder $pageData
     * @param View $view
     * @param bool $jsonEncoded
     * @return object
     */
    public function generatePage(\Jet\App\Engine\Core\Tables\PageBuilder $pageData, View $view, bool $jsonEncoded = true): object
    {
        if ($jsonEncoded) {
            $pageData->content = json_decode($pageData->content);
        }

        // todo better use custom class for this
        $page = new \stdClass();
        $page->pageData = $page;
        $page->html = $this->proceedContent($pageData->content, $view);
        return $page;
    }

    /**
     * @param array $pageData
     * @param View $view
     * @return string
     */
    private function proceedContent(array $pageData, View $view): string
    {
        $html = '';
        foreach ($pageData as $key => $element) {
            $children = '';
            if (!empty($element->children) && is_array($element->children)) {
                // do the same with children
                $children = $this->proceedContent($element->children, $view);
            }

            $html .= $this->proceedElement($element, $children, $view);
        }
        return $html;
    }

    /**
     * @param object $element
     * @param string $children
     * @param View $view
     * @return string
     */
    private function proceedElement(object $element, string $children, View $view): string
    {
        // todo also check if props->jet exists
        if ($element->type === 'chunk') {
            return $this->proceedChunk($element, $children, $view);
        } else {
            // just return plain string, there's nothing to worry about
            $templateName = $this->templatePrefix . $element->type;
            $view->_pb__disableLayout();
            $view->_pb__unsetForcedTemplateName();
            return $view->render($templateName, [
                'element' => $element,
                'children' => $children
            ]);
        }
    }

    /**
     * @param object $chunk
     * @param string $children
     * @param View $view
     * @return string
     */
    private function proceedChunk(object $chunk, string $children, View $view): string
    {
        // first of all we have to learn what type of class is it
        $type = $this->getChunkType($chunk->props->jet->class);
        // maybe it's me, but it looks unsafe
        switch ($type) {
            case 'controller':
                $className = NAMESPACE_ROOT_CLIENT . "\Controllers\\" . $chunk->props->jet->class;
                break;
            case 'widget':
                $className = NAMESPACE_ROOT_CLIENT . "\Widgets\\" . $chunk->props->jet->class;
                break;
            default:
                // there's nothing to do anymore
                return '';
                break;
        }

        if (!class_exists($className) || !method_exists($className, $chunk->props->jet->function) || !is_callable([$className, $chunk->props->jet->function])) {
            return '';
        }

        // that's not very good, actually
        $object = new $className();
        if (method_exists($object, 'setView')) {
            // there is huge possibility that we deal with Controller
            // since each action requires layout and have it's own template, disable layout and force template we need
            $forcedTemplateName = $this->generateTemplateString($chunk->props->jet);
            $view->_pb__disableLayout();
            $view->_pb__setForcedTemplateName($forcedTemplateName);
            $object->setView($view);
        }
        // note: if class is not controller, we have to implement render functionality
        // or use class view
        return $object->{$chunk->props->jet->function}($chunk, $children);
    }

    /**
     * @param object $jet
     * @return string
     */
    private function generateTemplateString(object $jet): string
    {
        $className = PHPJet::$app->tool->formatter->splitStringByCapitalLetter($jet->class);
        if (!$className || !isset($className[1])) {
            $className = 'default';
        } else {
            $className = strtolower($className[1]);
        }

        $functionName = PHPJet::$app->tool->formatter->splitStringByCapitalLetter($jet->function);
        if (!$functionName || !isset($functionName[1])) {
            $functionName = 'function';
        } else {
            $functionName = strtolower($functionName[1]);
        }

        return $this->templatePrefix . $className . $functionName;
    }

    /**
     * @param string $className
     * @return string
     */
    private function getChunkType(string $className): string
    {
        $classArray = PHPJet::$app->tool->formatter->splitStringByCapitalLetter($className);
        return isset($classArray[0]) ? strtolower($classArray[0]) : '';
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