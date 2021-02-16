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
    public $status = false;
    /**
     * @var string
     */
    public $message = 'Error';
    /**
     * @var \stdClass
     */
    public $customData;
}