<?php

namespace Jet\App\Engine\Core;

use Jet\App\Engine\Tools\ModelResponse;

/**
 *
 * Main handler of Model in MVC structure.
 * There is nothing to see.
 *
 */
/**
 * Class Model
 * @package Jet\App\Engine\Core
 */
class Model
{
    /**
     * @var string
     */
    private $name;

    /**
     * Model constructor.
     * @param string $name
     */
    public function __construct(string $name = "")
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param bool $status
     * @param string $message
     * @return ModelResponse
     */
    protected function sendResponseToController(bool $status = false, string $message = ''): ModelResponse
    {
        $response = new ModelResponse();
        $response->status = $status;
        $response->message = $message;
        return $response;
    }

    /**
     * @deprecated
     */
    public function getPagination()
    {

    }

    public function __clone()
    {

    }

    public function __wakeup()
    {

    }
}
