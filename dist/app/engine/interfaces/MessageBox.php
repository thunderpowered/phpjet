<?php


namespace Jet\App\Engine\Interfaces;

/**
 * Class MessageBox
 * @package Jet\App\Engine\Interfaces
 */
class MessageBox
{
    const SUCCESS = 0;
    const WARNING = 1;
    const ERROR   = 2;
    const DANGER  = 3;
    const INFO    = 4;
    /**
     * @var string[]
     */
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
    public function __construct(int $style = self::INFO, string $text = '')
    {
        $this->style = isset($this->styles[$style]) ? $this->styles[$style] : $this->styles[1];
        $this->text = $text;
    }
}