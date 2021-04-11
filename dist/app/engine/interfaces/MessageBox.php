<?php


namespace Jet\App\Engine\Interfaces;

/**
 * Class MessageBox
 * @package Jet\App\Engine\Interfaces
 */
class MessageBox
{
    private $styles = [
        'success',
        'warning',
        'error',
        'danger',
        'info'
    ];
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
    public function __construct(int $style = 1, string $text = '')
    {
        $this->style = isset($this->styles[$style]) ? $this->styles[$style] : $this->styles[1];
        $this->text = $text;
    }
}