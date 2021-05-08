<?php

namespace Jet\App\Engine\Exceptions;

/**
 * Class WrongDataException
 */
class WrongDataException extends CoreException
{
    /**
     * @var int
     */
    protected $code = 400;
    /**
     * @var string
     */
    protected $message = 'Bad Request';
}