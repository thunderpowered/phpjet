<?php


namespace CloudStore\App\Engine\Tools;

/**
 * Class Cache
 * @package CloudStore\App\Engine\Tools
 */
class Cache
{
    /**
     * @var string
     */
    private $cacheDirectory = './../cache/';
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

        // check date
        $isCacheStillAlive = $this->isCacheStillAlive($cacheLocation);
        if (!$isCacheStillAlive) {
            $this->deleteCacheFile($cacheLocation);
            return '';
        }

        // just return the fucking cache
        return file_get_contents($cacheLocation);
    }

    /**
     * @param string $page
     * @param string $identifier
     * @param string $data
     * @return bool|int
     */
    public function setCache(string $page, string $identifier, string $data)
    {
        $cacheLocation = $this->getCacheLocation($page, $identifier);
        return file_put_contents($cacheLocation, $data);
    }

    /**
     * @param string $cacheLocation
     * @return bool
     */
    private function deleteCacheFile(string $cacheLocation)
    {
        return unlink($cacheLocation);
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

    private function getCacheLocation(string $page, string $identifier): string
    {
        // dir structure:
        // /root
        // /root/page
        // /root/page/parameter.cache

        $dirName = $this->hashName($page);
        $dirPath = $this->cacheDirectory . '/' . $dirName;

        // that's not good
        if (!file_exists($dirPath)) {
            mkdir($dirPath);
        }

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
        return hash('sha256', $dirOrFileName);
    }
}