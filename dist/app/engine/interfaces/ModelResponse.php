<?php


namespace Jet\App\Engine\Interfaces;

/**
 * Class ModelResponse
 * @package Jet\App\Engine\Tools
 */
class ModelResponse
{
    /**
     * @var bool
     */
    public $status;
    /**
     * @var string
     */
    public $message;
    /**
     * @var array
     */
    public $customData;

    /**
     * ModelResponse constructor.
     * @param bool $status
     * @param string $message
     * @param array $customData
     */
    public function __construct(bool $status = false, string $message = '', array $customData = [])
    {
        $this->status = $status;
        $this->message = $message;
        $this->customData = $customData;
    }
}