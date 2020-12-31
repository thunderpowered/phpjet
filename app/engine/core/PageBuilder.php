<?php


namespace CloudStore\App\Engine\Core;

/**
 * Class PageBuilder
 * @package CloudStore\App\Engine\Core
 */
class PageBuilder
{
    /**
     * @var array
     */
    private $chunks = [
        'header' => [
            'top' => [],
            'middle' => []
        ],
        'sidebar' => [
            'left' => [],
            'right' => []
        ],
        'main' => [
            [
                //
                'id' => 'itemsGroupedByDate',
                'name' => 'Items grouped by date',
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
            ],
            [
                // etc...
            ]
        ],
        'footer' => [
            'top' => [],
            'bottom' => []
        ]
    ];
    /**
     * @var array
     * url => function
     */
    private $exceptions = [
        'api/quickSearch' => [
            'class' => 'ControllerSearch',
            'actionAJAXQuickSearch'
        ]
    ];

    /**
     * @param string $url
     * @return bool|array
     */
    public function getException(string $url)
    {
        return isset($this->exceptions[$url]) ? $this->exceptions[$url] : false;
    }

    /**
     * @param string $url
     * @return array
     */
    public function getPageData(string $url): array
    {
        // search in db and return array
    }
}