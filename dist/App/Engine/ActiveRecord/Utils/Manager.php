<?php


namespace Jet\App\Engine\Activerecord\Utils;

use Jet\App\Engine\ActiveRecord\Table;
use Jet\App\Engine\Config\Config;
use Jet\App\Engine\Config\Docs;
use Jet\App\Engine\Exceptions\CoreException;
use Jet\PHPJet;

/**
 * Class Manager
 * @package Jet\app\engine\activerecord\utils
 */
class Manager
{
    public const MIGRATE_MODE_HARD = 'MIGRATE_MODE_HARD';
    public const MIGRATE_MODE_SOFT = 'MIGRATE_MODE_SOFT';
    /**
     * @var string
     * todo load this from Config
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
     * @var
     */
    private $printEverything;

    /**
     * Manager constructor.
     */
    public function __construct(bool $printEverything = false)
    {
        $this->debug = Config::$dev['debug'];

        // while running from command line, you most likely want to see some output information
        $this->printEverything = $printEverything;
        // use local exception handler instead of global Error class
        set_exception_handler([$this, 'exceptionHandler']);
    }

    /**
     * @param string $mode
     * @param bool $save
     * @return bool
     * @throws CoreException
     */
    public function migrate(string $mode = self::MIGRATE_MODE_HARD, bool $save = true): bool
    {
        // prevent migrations if debug is off
        // this is essential thing, because migrations can really destroy the entire database and data in it
        // it can be dangerous to perform it in production mode
        // don't do it
        if (!$this->debug) {
            throw new CoreException('Migrations are disabled for production mode. See more information here: ' . Docs::returnDocLink('configure', 'migrations'));
        }

        if ($save) {
            $this->databaseBackup();
        }

        $tablesToUpdate = $this->checkTableStatuses();
        if (!$tablesToUpdate) {
            if ($this->printEverything) {
                print "Everything is up to date\n";
            }
            return false;
        }

        switch ($mode) {
            case self::MIGRATE_MODE_HARD:
                return $this->migrateHard($tablesToUpdate);
            case self::MIGRATE_MODE_SOFT:
                return $this->migrateSoft($tablesToUpdate);
            default:
                throw new CoreException("Invalid mode: $mode");
        }
    }

    /**
     * @return array
     */
    public function checkTableStatuses(): array
    {
        $tables = $this->parseTables("", false, false, true);
        $summary = $this->summarizeStatuses($tables);

        if ($this->printEverything) {
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
     * @param array $tables
     * @return bool
     * @throws CoreException
     */
    private function migrateHard(array $tables): bool
    {
        foreach ($tables as $table) {
            if (!($table['status'] instanceof _TableStatus) || (!($table['object'] instanceof Table))) {
                throw new CoreException('Argument passed into migrateSoft function has incorrect data type. It should be array of Table instances and _TableStatus instances.');
            }

            if ($table['status']->status === Checker::TABLE_STATUS_IGNORED || $table['status']->status === Checker::TABLE_STATUS_UP_TO_DATE) {
                continue;
            }

            // otherwise just drop and create
            $builder = new Builder($table['object']);
            $builder->createTable(true);
        }

        // well it returns true all the time, the only reason it doesn't - if error thrown
        return true;
    }

    /**
     * @param array $tables
     */
    private function migrateSoft(array $tables): bool
    {
        // todo
    }

    /**
     * @param array $tables
     * @return array
     * @description temporary
     */
    private function summarizeStatuses(array $tables): array
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
     * @param string $filterBy
     * @param bool $includeStructure
     * @param bool $includeData
     * @param bool $checkStatus
     * @return array
     */
    private function parseTables(string $filterBy = '', bool $includeStructure = false, bool $includeData = false, bool $checkStatus = false): array
    {
        // temp solution, i have an idea, but have no time
        $tables = scandir($this->databasePath);
        $tables = array_diff($tables, $this->remove);
        array_walk($tables, function (&$element) {
            $element = [
                'name' => substr($element, 0, strpos($element, '.'))
            ];
        });

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
                    $table['object'] = $object;
                } catch (CoreException $e) {
                    $this->exceptionHandler($e);
                }
            }
        }

        return array_values($tables);
    }

    /**
     * @throws CoreException
     */
    private function databaseBackup()
    {
        $backupFilename = PHPJet::$app->store->dump();
        if ($this->printEverything) {
            print "Backup file save to $backupFilename\n";
        }
    }

    /**
     * @param CoreException $exception
     */
    private function exceptionHandler(CoreException $exception)
    {
        PHPJet::$app->exit($exception->getNotes());
    }
}