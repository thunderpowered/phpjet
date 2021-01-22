<?php


namespace Jet\App\Engine\System;

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
    private $hashingAlgorithm = 'sha512';
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
            $this->csrfToken = $this->generateHash();
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
        if ($token && $this->csrfToken && $token === $this->csrfToken) {
            return true;
        }

        return false;
    }

    /**
     * @param string $algorithm
     * @return string
     */
    public function generateHash(string $algorithm = ''): string
    {
        if (!$algorithm) {
            $algorithm = $this->hashingAlgorithm;
        }
        return hash($algorithm, uniqid(rand(), true));
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