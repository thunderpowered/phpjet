<?php


namespace CloudStore\App\Engine\Tools;

use CloudStore\CloudStore;

/**
 * Class FileManager
 * @package CloudStore\App\Engine\Tools
 */
class FileManager
{
    /**
     * @var string
     */
    private $hashingAlgorithm = 'sha256';
    /**
     * @var array
     */
    private $allowedExtensions = [
        'jpg',
        'jpeg',
        'png'
        // will be extended
    ];
    /**
     * @var string
     */
    private $rootDirectory = WEB . 'storage';

    /**
     * FileManager constructor.
     */
    public function __construct()
    {
        if (!file_exists($this->rootDirectory)) {
            mkdir($this->rootDirectory);
        }
    }

    /**
     * @param string $location
     * @param array $file
     * @return string
     */
    public function saveNewFile(string $location, array $file): string
    {
        if (!$file || empty($file['tmp_name']) || empty($file['name'])) {
            return '';
        }

        $pathArray = explode('/', $location);
        // this is for placement file
        $filePath = $this->rootDirectory . '/';
        // this is for saving into database
        $fileURL = '';
        foreach ($pathArray as $path) {
            $filePathHashed = CloudStore::$app->system->token->hashString($path, $this->hashingAlgorithm);
            $filePath .= $filePathHashed . '/';
            $fileURL .= $filePathHashed . '/';

            if (!file_exists($filePath)) {
                mkdir($filePath);
            }
        }

        // as far as i know this is the best way to get extension
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (!in_array($fileExtension, $this->allowedExtensions)) {
            return '';
        }

        // let's generate random name?
        $fileName = CloudStore::$app->system->token->generateHash($this->hashingAlgorithm) . ".{$fileExtension}";
        $filePath = $filePath . $fileName;
        $fileURL = $fileURL . $fileName;

        move_uploaded_file($file['tmp_name'], $filePath);
        return $fileURL;
    }

    /**
     * @param string $filePath
     * @return bool
     */
    public function deleteFile(string $filePath): bool
    {
        $filePath = $this->rootDirectory . '/' . $filePath;
        if (!file_exists($filePath)) {
            return false;
        }

        return unlink($filePath);
    }

    /**
     * @param string $string
     * @return string
     */
    private function hashString(string $string): string
    {
        return hash('sha256', $string);
    }
}