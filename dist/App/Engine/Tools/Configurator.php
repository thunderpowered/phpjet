<?php

namespace Jet\App\Engine\Tools;

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
    private $argv = [];
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
     * @var bool
     */
    private $debug;

    /**
     * Configurator constructor.
     */
    public function __construct()
    {
        $this->debug = Config::$dev['debug'];
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
        if (!$this->debug) {
            throw new CoreException('Migrations are disabled for production mode. See more information here: https://phpjet.org/docs/configure');
        }
        $this->argv = $argv;
        // backup is
        // available in backups/database/
        if (in_array('--save', $this->argv)) {
            $this->migrateBackup($this->argv);
        }

        $tablesToUpdate = $this->migrateCheckTables();
        if (!$tablesToUpdate) {
            return "Everything is up to date\n";
        }

        if (in_array('--hard', $this->argv)) {
            $this->migrateHard($tablesToUpdate);
        } else if (in_array('--soft', $this->argv)) {
            $this->migrateSoft($tablesToUpdate);
        }

        return '';
    }

    /**
     * @param array $argv
     * @return string
     * @throws CoreException
     */
    public function createuser(array $argv = []): string
    {
        if (!$this->debug) {
            throw new CoreException('Creating superusers is disabled for production mode. Set \'debug\' to true in Config.php if you want to continue');
        }

        $params = $this->proceedArgvParams($argv, [
            '-login' => true,
            '-password' => true,
            '--2f' => false
        ]);

        // todo maybe create function for parsing params
        $loginIndex = array_search('-login', $argv);
        if ($loginIndex === false || !isset($argv[$loginIndex + 1])) {
            throw new CoreException('flag -login is required');
        }
        $login = $argv[$loginIndex + 1];

        $passwordIndex = array_search('-password', $argv);
        if ($passwordIndex === false || !isset($argv[$passwordIndex + 1])) {
            throw new CoreException('flag -password is required');
        }
        $password = $argv[$passwordIndex + 1];

        $twoFactorAuth = in_array('--2f', $argv);

        // todo do something special
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

                // local exception handler doesn't work when running from cmd
                // todo set up exception/fatal error handlers properly
                try {
                    $table['status'] = $object->returnStatus();
                } catch (CoreException $e) {
                    $this->exceptionHandler($e);
                }
            }
        }

        return array_values($tables);
    }

    /**
     * @param array $tables
     * @return array
     * temporary
     */
    private function migrateSummarizeStatuses(array $tables): array
    {
        $results = [];
        foreach ($tables as $table) {
            $status = $table['status']->status;
            if (isset($results[$status])) {
                $results[$status]++;
            } else {
                $results[$status] = 1;
            }
        }
        return $results;
    }

    /**
     * returns array of tables that have to be updated
     * @param array $argv
     * @return array
     */
    private function migrateCheckTables(): array
    {
        $tables = $this->_parseTables("", false, false, true);
        $summary = $this->migrateSummarizeStatuses($tables);

        if (in_array('-p', $this->argv)) {
            print (
            (isset($summary[0]) ? "Tables ignored: $summary[0]\n" : "") .
            (isset($summary[1]) ? "Tables to be created: $summary[1]\n" : "") .
            (isset($summary[2]) ? "Tables to be updated: $summary[2]\n" : "") .
            (isset($summary[3]) ? "Tables that are up-to-date: $summary[3]\n" : "")
            );
        }

        return $tables;
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
        print ("let's make a fucking update!!!\n");
        // todo do something
    }

    /**
     * @param array $argv
     * @param array $params
     * @return array
     */
    private function proceedArgvParams(array $argv, array $params = []): array
    {
        return [];
    }
}