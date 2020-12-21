<?php

namespace CloudStore\App\Engine\Config;

/**
 * Class Database
 * @package CloudStore\App\Engine\Config
 * Connect to Database. MySQL with PDO extension in use.
 */
class Database
{
    /**
     * @var array
     */
    public static array $tables;
    /**
     * @var \PDO
     */
    private static \PDO $_db;
    /**
     * @var array
     */
    private static array $opt = array(
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES => false
    );
    /**
     * @var array
     */
    private static array $config;
    /**
     * Database constructor.
     */
    private function __construct()
    {

    }
    /**
     * @param array $config
     */
    public static function setConfig(array $config): void
    {
        Database::$config = $config;
    }
    /**
     * @return array
     */
    public static function showTables(): array
    {
        $stmt = Database::getInstance()->query("show full tables where Table_Type = 'BASE TABLE'");
        $stmt->execute();
        $temp = $stmt->fetchAll();

        // prepare
        $tables = [];
        foreach ($temp as $key => $value) {
            $tables[] = $value["Tables_in_" . Database::$config['database']];
        }
        
        return $tables;
    }
    /**
     * @return \PDO
     */
    public static function getInstance(): \PDO
    {
        if (!Database::$_db) {

            try {

                Database::$_db = new \PDO("mysql:host=" . Database::$config['host'] . ";dbname=" . Database::$config['database'] . ";charset=utf8", Database::$config['username'], Database::$config['password'], Database::$opt);
            } catch (\Exception $e) {

                die("Unable to connect to Database");
            }
        }

        return Database::$_db;
    }
    private function __clone()
    {

    }
    private function __wakeup()
    {

    }
}
