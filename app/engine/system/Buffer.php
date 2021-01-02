<?php

namespace CloudStore\App\Engine\System;

/**
 *
 * Component: ShopEngine Buffer
 * Description: ShopEngine automatically compress the HTML. It helps to increase speed of loading page.
 *
 * Comment: I'll give you choose (if you don't want to use it) in next updates.
 *
 */

/**
 * Class Buffer
 * @package CloudStore\App\Engine\System
 */
class Buffer
{
    /**
     * @var array
     */
    private $compressSearch = [
        '/\>[^\S ]+/s',
        '/[^\S ]+\</s',
        '/(\s)+/s',
        '/<!--(.*?)-->/'
    ];

    /**
     * @var array
     */
    private $compressReplace = [
        '>',
        '<',
        '\\1',
        ''
    ];

    /**
     * Buffer constructor.
     */
    public function __construct()
    {
    }

    public function createBuffer()
    {
        ob_start();
    }

    /**
     * @param bool $create
     */
    public function clearBuffer(bool $create = false)
    {
        if (ob_get_length()) {
            ob_clean();
        }
        if ($create) {
            $this->createCompressedBuffer();
        }
    }

    /**
     * @param bool $return
     * @return string
     */
    public function destroyBuffer(bool $return = false): string
    {
        $output = '';
        if (ob_get_length()) {
            $output = ob_end_clean();
        }
        return $output;
    }

    public function createCompressedBuffer()
    {
        ob_start([$this, "compressOutput"]);
    }

    /**
     * @param bool $clean
     * @return string
     */
    public function returnBuffer(bool $clean = true): string
    {
        if ($clean) {
            return ob_get_clean();
        } else {
            return ob_get_contents();
        }
    }

    /**
     * @param string $output
     * @return string
     */
    private function compressOutput(string $output): string
    {
        return preg_replace($this->compressSearch, $this->compressReplace, $output);
    }
}
