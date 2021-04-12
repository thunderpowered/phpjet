<?php


namespace Jet\App\Engine\Tools;

/**
 * Class Validator
 * @package Jet\App\Engine\Tools
 */
class Validator
{
    /**
     * @param string $email
     * @return bool
     */
    public static function validateEmail(string $email): bool
    {
        return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @param string $url
     * @param bool $hostRequired
     * @param bool $schemeRequired
     * @param bool $pathRequired
     * @return bool
     */
    public static function validateURL(string $url, bool $hostRequired = false, bool $schemeRequired = false, bool $pathRequired = false): bool
    {
        $flags = 0;
        if ($hostRequired) {
            $flags = $flags | FILTER_FLAG_HOST_REQUIRED;
        }
        if ($schemeRequired) {
            $flags = $flags | FILTER_FLAG_SCHEME_REQUIRED;
        }
        if ($pathRequired) {
            $flags = $flags | FILTER_FLAG_PATH_REQUIRED;
        }
        return (bool)filter_var($url, FILTER_VALIDATE_URL, $flags);
    }

    /**
     * @param string $password
     * @return bool
     */
    public static function validatePassword(string $password): bool
    {
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
            return false;
        } else {
            return true;
        }
    }
}