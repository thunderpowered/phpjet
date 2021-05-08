<?php

namespace Jet\App\Engine\Config;

use Exception;
use PDO;

/**
 * Class Database
 * @package Jet\App\Engine\Config
 * Connect to Database. MySQL with PDO extension in use.
 */
class Database
{
    /**
     * @var array
     */
    public static $tables;
    /**
     * @var PDO
     */
    private static $_db;
    /**
     * @var array
     */
    private static $opt = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    );
    /**
     * @var array
     */
    private static $config;

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
     * @return PDO
     */
    public static function getInstance(): PDO
    {
        if (!Database::$_db) {
            try {
                Database::$_db = new PDO(
                    "mysql:host=" . Database::$config['host'] . ";dbname=" . Database::$config['database'] . ";charset=utf8mb4",
                    Database::$config['username'],
                    Database::$config['password'],
                    self::$opt
                );
            } catch (Exception $e) {
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
