<?php

namespace Jet\App\Engine\Components;
/**
 * Class Auth
 * @package Jet\App\Engine\Components
 */
class Auth
{
    /**
     * @return bool
     */
    public function isUserAuthorized(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function authorizeUser(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function deauthorizeUser(): bool
    {
        return false;
    }
}