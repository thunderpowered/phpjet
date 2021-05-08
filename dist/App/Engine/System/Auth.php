<?php


namespace Jet\App\Engine\System;

/**
 * Class Auth
 * @package Jet\App\Engine\System
 */
class Auth
{
    private const AUTH_MODE_DEFAULT = 'AUTH_MODE_DEFAULT';
    private const AUTH_MODE_JWT = 'AUTH_MODE_DEFAULT';
    /**
     * @var string
     */
    private $mode;

    /**
     * Auth constructor.
     * @param string $mode
     */
    public function __construct(string $mode = self::AUTH_MODE_DEFAULT)
    {
        $this->mode = $mode;
    }

    /**
     * @return bool
     */
    public function isAuthorized(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function deauthorize(): bool
    {
        return false;
    }
}