<?php


namespace CloudStore\App\Engine\System;

/**
 * Class Token
 * @package CloudStore\App\Engine\System
 */
class Token
{
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
        $this->csrfToken = $_SESSION['token'] ?? null;
    }

    /**
     * @return string
     */
    public function generateToken(): string
    {
        if (!$this->csrfToken) {
            $_SESSION['token'] = $this->csrfToken = $this->generateHash();
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
     * @return string
     */
    public function generateHash(): string
    {
        return hash($this->hashingAlgorithm, uniqid(rand(), true));
    }

    /**
     * @param string $string
     * @return string
     */
    public function hashString(string $string): string
    {
        return hash($this->hashingAlgorithm, $string);
    }
}