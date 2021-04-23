<?php

namespace Jet\App\Engine\Tools;

use Exception;
use Jet\App\Engine\ActiveRecord\_FieldType;
use Jet\App\Engine\ActiveRecord\Table;
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
     * @var string
     */
    private $databasePath = APP . 'database/';
    /**
     * @var string
     */
    private $databaseNamespace = NAMESPACE_APP . 'Database\\';
    /**
     * @var string[]
     */
    private $remove = [
        '.', '..'
    ];

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

        $tablesToUpdate = $this->migrateCheckTables();
        if (!$tablesToUpdate) {
            return "Everything is up to date\n";
        }

        if (in_array('--hard', $argv)) {
            $this->migrateHard($tablesToUpdate);
        } else if (in_array('--soft', $argv)) {
            $this->migrateSoft($tablesToUpdate);
        }

        return '';
    }

    /**
     * returns array of all tables
     * @param string $filterBy
     * @param bool $includeStructure
     * @param bool $includeData
     * @param bool $checkStatus
     * @return array
     */
    public function _parseTables(string $filterBy = '', bool $includeStructure = false, bool $includeData = false, bool $checkStatus = false): array
    {
        // temp solution, i have an idea, but have no time
        $tables = scandir($this->databasePath);
        $tables = array_diff($tables, $this->remove);
        array_walk($tables, function (&$element) {
            $element = [
                'name' => substr($element, 0, strpos($element, '.'))
            ];
        });

        // filtering
        if ($filterBy) {
            $tables = array_filter($tables, function ($element) use ($filterBy) {
                return $element['name'] === $filterBy;
            });
        }

        if ($includeStructure) {
            // do something good
        }

        if ($includeData) {
            // do something even more good
        }

        /**
         * 0 - table does not exist
         * 1 - table exists, yet outdated
         * 2 - table exists and up to date
         */
        if ($checkStatus) {
            foreach ($tables as &$table) {
                $className = $this->databaseNamespace . $table['name'];
                /**
                 * @var Table $object
                 */
                $object = new $className();
                $tableName = $object->_getDBTableName();

                // check if exists
                $exists = PHPJet::$app->store->doesTableExist($tableName);
                if (!$exists) {
                    $table['status'] = 0;
                    continue;
                }
                // check structure
                $structure = PHPJet::$app->store->getTableStructure($tableName);
                // todo check structure
            }
        }

        return array_values($tables);
    }

    /**
     * returns array of tables that have to be updated
     * @return array
     */
    private function migrateCheckTables(): array
    {
        $tables = $this->_parseTables("", false, false, true);
        foreach ($tables as $key => $table) {
            echo $table['status'] . " " . $table['name'] . "\n";
        }
        return [];
    }

    /**
     * @throws CoreException
     */
    private function migrateBackup(array $argv = [])
    {
        $backupFilename = PHPJet::$app->store->dump();
        if (in_array('-p', $argv)) {
            print 'Backup file save to ' . $backupFilename . "\n";
        }
    }

    /**
     * Hard migration basically means that the database will be entirely vanished, then created from scratch
     * In fact this is the most reliable way to do it
     * @param array $tables
     */
    private function migrateHard(array $tables)
    {
        // todo do something
    }

    /**
     * Soft migration will try to perform changed without deleting database
     * Doesn't work in most cases
     * Especially if database is full of data
     * @param array $tables
     */
    private function migrateSoft(array $tables)
    {
        // todo do something
    }
}