<?php


namespace Jet\App\Engine\System;

use Exception;
use Jet\App\Engine\Exceptions\CoreException;
use Jet\PHPJet;

/**
 * Class Token
 * @package Jet\App\Engine\System
 */
class Token
{
    /**
     * @var string
     */
    private $sessionCSRFTokenKey = '__csrf_token';
    /**
     * @var string
     */
    private $csrfToken;
    /**
     * @var string
     */
    private $hashingAlgorithm = 'sha3-256';
    /**
     * @var string
     */
    public $headerCSRFTokenKey = 'X_CSRF_TOKEN';
    /**
     * Token constructor.
     */
    public function __construct()
    {
        $this->csrfToken = $_SESSION[$this->sessionCSRFTokenKey] ?? null;
    }

    /**
     * @return string
     */
    public function generateToken(): string
    {
        if (!$this->csrfToken) {
            $this->csrfToken = $this->generateRandomString(64, $this->hashingAlgorithm);
            PHPJet::$app->system->request->setSESSION($this->sessionCSRFTokenKey, $this->csrfToken);
        }
        return $this->csrfToken;
    }

    /**
     * @param $token
     * @return bool
     */
    public function validateToken(string $token): bool
    {
        return $token && $this->csrfToken && $token === $this->csrfToken;
    }

    /**
     * @return bool
     */
    public function checkCSRFToken(): bool
    {
        $csrfToken = PHPJet::$app->system->request->getSERVER("HTTP_" . $this->headerCSRFTokenKey);
        return $this->validateToken($csrfToken);
    }

    /**
     * @param int $length
     * @param string $hashingAlgorithm
     * @return string
     */
    public function generateRandomString(int $length = 64, string $hashingAlgorithm = ''): string
    {
        $string = null;
        try {
            $string = bin2hex(random_bytes($length));
        } catch (Exception $e) {
            PHPJet::$app->exit('Token: impossible to generate a token. See more info here: https://www.php.net/manual/en/function.random-bytes.php');
        }
        if ($hashingAlgorithm) {
            // i'm really not sure should i hash the string
            // probably it gives almost nothing, but slows down the application a bit
            // i'll think about it later
            $string =  hash($hashingAlgorithm, $string);
        }
        return $string;
    }

    /**
     * @param string $string
     * @param string $algorithm
     * @return string
     */
    public function hashString(string $string, string $algorithm = ''): string
    {
        if (!$algorithm) {
            $algorithm = $this->hashingAlgorithm;
        }
        return hash($algorithm, $string);
    }
}