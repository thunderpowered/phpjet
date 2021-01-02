<?php

namespace CloudStore\App;

/*
 *
 * Startup file.
 * Loading all components and start the engine.
 *
 */

use CloudStore\App\Engine\Config\Config;
use CloudStore\App\Engine\Config\ConfigManager;
use CloudStore\App\Engine\Config\Database;
use CloudStore\App\Engine\Core\PageBuilder;
use CloudStore\App\Engine\Core\Router;
use CloudStore\App\Engine\Core\Selector;
use CloudStore\App\Engine\Core\Store;
use CloudStore\App\Engine\Core\System;
use CloudStore\App\Engine\Core\Tool;
use CloudStore\App\Engine\System\Error;
use CloudStore\CloudStore;

/**
 * Class Startup
 * @package CloudStore\App\Engine
 */
class App
{
    /**
     * @var Router
     */
    public $router;
    /**
     * @var Store
     */
    public $store;
    /**
     * @var System
     */
    public $system;
    /**
     * @var Tool
     */
    public $tool;
    /**
     * @var Error
     */
    public $error;
    /**
     * @var PageBuilder
     */
    public $pageBuilder;
    /**
     * @var ConfigManager
     */
    private $configManager;

    /**
     * App constructor.
     */
    public function __construct()
    {
        session_start();
        // Set main loader
        spl_autoload_register(array($this, "classLoader"));
        // Set config loader, because configs may be located in different directories
        spl_autoload_register(array($this, "configLoader"));

        // Prepare some things
        $this->router = new Router();
        $this->store = new Store();
        $this->system = new System();
        $this->tool = new Tool();
        $this->configManager = new ConfigManager();
        $this->pageBuilder = new PageBuilder();

        // Set up the error handler
        $this->error = new Error();
    }

    /**
     * @return string
     */
    public function start()
    {
        // Select the config
        $configSelector = new Selector();
        $configSelector->select();

        // prepare class Store
        Database::setConfig(Config::$db);
        $this->store->setDB(Database::getInstance());
        $this->store->prepareTables();

        // Set the config
        // All data for config is usually loading from database
        $this->configManager->prepareConfig();

        // Start the engine
        return $this->execute($this->router);
    }

    /**
     * @param Router $router
     * @return string
     */
    public function execute(Router $router): string
    {
        return $router->start();
    }

    /**
     * @param string $message
     */
    public function exit(string $message = '')
    {
        CloudStore::$app->system->buffer->clearBuffer();
        if ($message) {
            $message = "\n\r" . 'CloudStore Engine Shutdown Message: ' . $message . "\n\r";
        }
        exit($message);
    }

    /**
     * @param string $fileName
     */
    private function load(string $fileName)
    {
        if (file_exists($fileName) && !is_dir($fileName)) {
            require_once $fileName;
        }
    }

    /**
     * @param string $className
     */
    private function classLoader(string $className)
    {
        $fileName = ROOT . $this->getFileName($className);
        $this->load($fileName);
    }

    /**
     * @param string $className
     * @return string
     */
    private function getFileName(string $className): string
    {
        // important note: namespaces must correspond real file paths
        // and more important note: all namespaces should start with NAMESPACE_ROOT value
        $path = explode("\\", $className);

        // take last
        $file = array_pop($path);

        // lower every part
        foreach ($path as $key => $value) {
            $path[$key] = strtolower($value);
        }

        // as i mentioned before, first part of namespace should contain root namespace name
        $first = $path[0];
        $nameSpaceRoot = strtolower(NAMESPACE_ROOT);
        if ($first !== $nameSpaceRoot) {
            return false;
        };

        // and remove first element
        array_shift($path);

        // and implode it into string
        $path = implode('/', $path);

        // create filename
        $file = $file . ".php";

        // return created filepath
        return $path . '/' . $file;
    }

    /**
     * @param string $className
     */
    private function configLoader(string $className)
    {
        $path = explode("\\", $className);
        $file = $path[count($path) - 1] . ".php";
        if (defined("CONFIG_DIR")) {

            $fileName = CONFIG_DIR . $file;
            $this->load($fileName);
        }
    }
}
