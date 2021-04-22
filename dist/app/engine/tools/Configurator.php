<?php

namespace Jet\App\Engine\Tools;

use Exception;
use Jet\App\Engine\Config\Config;
use Jet\App\Engine\Exceptions\CoreException;
use Jet\PHPJet;

/**
 * Class Configurator
 * @package Jet\App\Engine\Tools
 */
class Configurator
{
    /**
     * @var array
     */
    private $argv;

    /**
     * Configurator constructor.
     */
    public function __construct()
    {
        // use local exception handler
        set_exception_handler([$this, 'exceptionHandler']);
    }

    /**
     * @param CoreException $exception
     */
    public function exceptionHandler(CoreException $exception)
    {
        PHPJet::$app->exit($exception->getNotes());
    }

    /**
     * @param array $argv
     * @return string
     * @throws CoreException
     */
    public function migrate(array $argv = []): string
    {
        // prevent migrations if debug is off
        // this is essential thing, because migrations can really kill the entire database and data in it
        // it can be dangerous to perform it in production mode
        // don't do it
        if (!Config::$dev['debug']) {
            throw new CoreException('Migrations are disabled for production mode. See more information here: https://phpjet.org/docs/configure');
        }
        // backup is available in backups/database/
        if (in_array('--save', $argv)) {
            $this->migrateBackup($argv);
        }
        if (in_array('--hard', $argv)) {
            $this->migrateHard();
        } else if (in_array('--soft', $argv)) {
            $this->migrateSoft();
        }

        return '';
    }

    /**
     * @throws CoreException
     */
    private function migrateBackup(array $argv = [])
    {
        $backupFilename = PHPJet::$app->store->dump();
        if (in_array('-p', $argv)) {
            print 'Backup file save to ' . $backupFilename;
        }
    }

    /**
     * Hard migration basically means that the database will be entirely vanished, then created from scratch
     * In fact this is the most reliable way to do it
     */
    private function migrateHard()
    {
        // todo do something
    }

    /**
     * Soft migration will try to perform changed without deleting database
     * Doesn't work in most cases
     * Especially if database is full of data
     */
    private function migrateSoft()
    {
        // todo do something
    }
}