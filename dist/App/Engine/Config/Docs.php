<?php

namespace Jet\App\Engine\Config;

/**
 * Class Docs
 * @package Jet\App\Engine\Config
 * @description Sometimes we need to show users a message where they can find more information on a particular issue.
 * The documentation can change, so in order not to search through the entire code for links,
 * i decided to make one collection of all links to the documentation.
 * A specific solution, but it works for now.
 */
class Docs
{
    public static $main = [
        'url' => 'https://phpjet.org/docs'
    ];
    public static $docs = [
        [
            'configure' => ['/configure', [
                    'migrations' => '/migrations'
                ]
            ]
        ]
    ];

    public static function returnDocLink(string $docSection, string $docName): string
    {
        if (!isset(self::$docs[$docSection]) || !isset(self::$docs[$docSection][1]) || !isset(self::$docs[$docSection][1][$docName])) {
            return self::$main['url'];
        } else {
            return self::returnCombinedURL($docSection, $docName);
        }
    }

    /**
     * @param string $docSection
     * @param string $docName
     * @return string
     */
    private static function returnCombinedURL(string $docSection, string $docName): string
    {
        return self::$main['url'] . self::$docs[$docSection] . self::$docs[$docSection][1][$docName];
    }
}