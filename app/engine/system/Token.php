<?php


namespace CloudStore\App\Engine\System;

use CloudStore\App\Engine\Components\Request;

/**
 * Class Token
 * @package CloudStore\App\Engine\System
 */
class Token
{
    /**
     * @return string
     */
    public static function generateToken(): string
    {
        // Generate CSRF-token. If you want to protect some action, write \CloudStore\App\Engine\Components\Utils::generate_token(), then use validate to check it.
        // This method will be improved in next version of SE.
        // After every action, the token will die and generate again.
        // It'll get more security for application.
        // Current solution is very simple, but it works in most cases.

        if (!isset($_SESSION['token'])) {
            $_SESSION['token'] = hash("sha256", uniqid(rand(), true));
        }
        return $_SESSION['token'];
    }

    /**
     * @param $token
     * @return bool
     */
    public static function validateToken($token): bool
    {
        // This method check the token
        if (isset($_SESSION['token']) AND $_SESSION['token'] === $token) {
            return true;
        }
        return false;
    }

    /**
     * @param $token
     * @return bool
     */
    public static function validateAction($token): bool
    {
        // This method crashes the application if something wrong
        // It's more simple to use. Just write Utils::validate_action();
        if (empty($token)) {
            $token = Request::get('token');
        }
        if (isset($_SESSION['token']) AND $_SESSION['token'] === $token) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public static function generateCheckoutToken(): string
    {
        // Separate method for checkout
        // I have no idea why i did it
        if (!isset($_SESSION['checkout_token'])) {
            $_SESSION['checkout_token'] = hash("sha256", uniqid(rand(), true));
        }
        return $_SESSION['checkout_token'];
    }

    /**
     * @param $token
     * @return bool
     */
    public static function validateCheckoutToken($token): bool
    {
        if (!empty($_SESSION['checkout_token']) AND $_SESSION['checkout_token'] === $token) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public static function generateHash(): string
    {
        return hash("sha256", uniqid(rand(), true));
    }
}