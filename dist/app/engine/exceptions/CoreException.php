<?php


namespace Jet\App\Engine\Exceptions;

use Exception;
use Throwable;

/**
 * Class CoreException
 * @package Jet\App\Engine\Exceptions
 */
class CoreException extends Exception
{
    /**
     * @var string
     */
    protected $notes = '';
    /**
     * @var int
     */
    protected $code = 500;
    /**
     * @var string
     */
    protected $message = 'Internal Server Error';

    /**
     * CoreException constructor.
     * @param string $notes
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($notes = "", $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->notes = $notes;
    }

    /**
     * @return string
     */
    public function getNotes(): string
    {
        return $this->notes;
    }
}