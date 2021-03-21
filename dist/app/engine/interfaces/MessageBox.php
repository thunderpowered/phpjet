<?php


namespace Jet\App\Engine\Interfaces;

/**
 * Class MessageBox
 * @package Jet\App\Engine\Interfaces
 */
class MessageBox
{
    /**
     * @var bool
     */
    public $style;
    /**
     * @var string
     */
    public $text;

    /**
     * MessageBox constructor.
     * @param int $style
     * @param string $text
     */
    public function __construct(int $style = 0, string $text = '')
    {
        $this->style = $style;
        $this->text = $text;
    }
}