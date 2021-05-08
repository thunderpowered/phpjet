<?php


namespace Jet\App\Engine\Tools;

use Jet\PHPJet;

/**
 * Class Cache
 * @package Jet\App\Engine\Tools
 */
class Cache
{
    /**
     * @var string
     */
    private $cacheDirectory = ROOT . 'cache/';
    /**
     * @var int
     */
    private $cacheLifeSpan = 3600;
    /**
     * @var string
     */
    private $cacheFileExtension = '.cache';

    /**
     * Cache constructor.
     */
    public function __construct()
    {
        if (!file_exists($this->cacheDirectory)) {
            mkdir($this->cacheDirectory);
        }

        $this->cacheDirectory = realpath($this->cacheDirectory);
    }

    /**
     * @param string $page
     * @param string $identifier
     * @return string
     */
    public function getCache(string $page, string $identifier): string
    {
        $cacheLocation = $this->getCacheLocation($page, $identifier);
        if (!file_exists($cacheLocation)) {
            return '';
        }

        $isCacheStillAlive = $this->isCacheStillAlive($cacheLocation);
        if (!$isCacheStillAlive) {
            $this->deleteCacheFile($cacheLocation);
            return '';
        }

        return file_get_contents($cacheLocation);
    }

    /**
     * @return bool
     */
    public function manageCache(): bool
    {
        return true;
    }

    /**
     * @param string $page
     * @param string $identifier
     * @param string $data
     * @return bool|int
     */
    public function setCache(string $page, string $identifier, string $data)
    {
        $cacheDir = $this->getCacheDirectory($page);
        if (!file_exists($cacheDir)) {
            mkdir($cacheDir);
        }

        $cacheLocation = $this->getCacheLocation($page, $identifier);
        return file_put_contents($cacheLocation, $data);
    }

    /**
     * @param string $cacheLocation
     * @return void
     */
    private function deleteCacheFile(string $cacheLocation): void
    {
        unlink($cacheLocation);
    }

    /**
     * @param string $cacheLocation
     * @return bool
     */
    private function isCacheStillAlive(string $cacheLocation): bool
    {
        $fileTime = filemtime($cacheLocation);
        $currentTime = time();
        $difference = $currentTime - $fileTime;
        if ($difference > $this->cacheLifeSpan) {
            return false;
        }
        return true;
    }

    /**
     * @param string $page
     * @return string
     */
    private function getCacheDirectory(string $page): string
    {
        $dirName = $this->hashName($page);
        return $this->cacheDirectory . '/' . $dirName;
    }

    /**
     * @param string $page
     * @param string $identifier
     * @return string
     */
    private function getCacheLocation(string $page, string $identifier): string
    {
        // dir structure:
        // /root
        // /root/page
        // /root/page/parameter.cache

        $dirPath = $this->getCacheDirectory($page);
        $fileName = $this->getCacheFileName($identifier);
        return $dirPath . '/' . $fileName;
    }

    /**
     * @param string $identifier
     * @return string
     */
    private function getCacheFileName(string $identifier): string
    {
        return $this->hashName($identifier) . $this->cacheFileExtension;
    }

    /**
     * @param string $dirOrFileName
     * @return string
     */
    private function hashName(string $dirOrFileName): string
    {
        return PHPJet::$app->system->token->hashString($dirOrFileName);
    }
}