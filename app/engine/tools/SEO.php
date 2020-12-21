<?php

namespace CloudStore\App\Engine\Tools;

use CloudStore\App\Engine\Core\Controller;

/**
 * Class SEO
 * @package CloudStore\App\Engine\Tools
 * @desctiption: Generating meta-information for page
 */
class SEO
{
    /**
     * @param Controller $controller
     * @return string
     */
    public static function getMetaTags(Controller $controller): string
    {
        if (!method_exists($controller, 'SEO')) {
            return '';
        }

        $meta = [];
        $array = $controller->SEO();
        foreach ($array as $name => $key) {
            foreach ($key as $prop => $value) {
                $meta[] = '<meta ' . $name . '="' . $prop . '" content="' . $value . '">';
            }
        }

        $meta = implode("\r\n", $meta);
        return $meta;
    }
}
