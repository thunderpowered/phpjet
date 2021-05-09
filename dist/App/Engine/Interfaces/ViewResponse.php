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
     * @var int
     */
    public $status;

    /**
     * ViewResponse constructor.
     * @param bool $SPA
     * @param string $response
     * @param int $status
     */
    public function __construct(bool $SPA = false, string $response = '', int $status = HTTP_OK)
    {
        $this->SPA = $SPA;
        $this->response = $response;
        $this->status = $status;
    }
}