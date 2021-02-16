<?php


namespace Jet\App\Engine\Interfaces;

/**
 * Class ViewResponse
 * @package Jet\App\Engine\Interfaces
 */
class ViewResponse
{
    /**
     * @var bool
     */
    public $SPA;
    /**
     * @var string
     */
    public $response;

    /**
     * ViewResponse constructor.
     * @param bool $SPA
     * @param string $response
     */
    public function __construct(bool $SPA = false, string $response = '')
    {
        $this->SPA = $SPA;
        $this->response = $response;
    }
}