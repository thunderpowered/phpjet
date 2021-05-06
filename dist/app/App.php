<?php

namespace Jet\App;

/*
 *
 * Startup file.
 * Loading all components and start the engine.
 *
 */

use Jet\App\Engine\Config\Config;
use Jet\App\Engine\Config\ConfigManager;
use Jet\App\Engine\Config\Database;
use Jet\App\Engine\Core\Router;
use Jet\App\Engine\Core\Selector;
use Jet\App\Engine\Core\Store;
use Jet\App\Engine\Core\System;
use Jet\App\Engine\Core\Tool;
use Jet\App\Engine\System\Error;
use Jet\App\Engine\System\PageBuilder;
use Jet\PHPJet;
use mysql_xdevapi\Exception;

/**
 * Class Startup
 * @package Jet\App\Engine
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
     * @param string $mode
     * @throws Engine\Exceptions\CoreException
     */
    public function __construct(string $mode = 'start')
    {
        // Set main loader
        spl_autoload_register(array($this, "classLoader"));
        // Set config loader, because configs may be located in different directories
        spl_autoload_register(array($this, "configLoader"));

        // load components
        $this->store = new Store();
        $this->tool = new Tool();
        $this->configManager = new ConfigManager();

        // load specific components for different modes
        switch ($mode) {
            case 'start':
                $this->router = new Router();
                $this->system = new System();
                $this->pageBuilder = new PageBuilder();
                break;
            // there were different options, now is only one
        }

        // Set up the error handler
        $this->error = new Error();

        // Select the config
        $configSelector = new Selector();
        $configSelector->select();
    }

    /**
     * @return string
     */
    public function start(): string
    {
        // prepare class Store
        Database::setConfig(Config::$db);
        $this->store->setDB(Database::getInstance(), Config::$db['database']);
        $this->store->prepareTables();

        // Set the config
        // All data for config is usually loading from database
        $this->configManager->prepareConfig();

        return $this->router->start();
    }

    /**
     * @param array $argv
     * @return string
     */
    public function configure(array $argv = []): string
    {
        $functionName = $argv[2] ?? null;
        if ($functionName
            && substr($functionName, 0, 1) !== '_'
            && method_exists($this->tool->configurator, $functionName)) {
            // prepare database
            Selector::__legacySelect();
            Database::setConfig(Config::$db);
            $this->store->setDB(Database::getInstance(), Config::$db['database']);
            $this->store->prepareTables();

            try {
                return call_user_func([$this->tool->configurator, $functionName], $argv);
            } catch (Exception $exception) {
                $this->exit($exception->getMessage());
            }
        } else {
            return "Unable to configure: method does not exist";
        }
    }

    /**
     * @param string $message
     * @param bool $showTitle
     */
    public function exit(string $message = '', bool $showTitle = false)
    {
        if (PHPJet::$app->system) { // can be disabled in configure mode
            PHPJet::$app->system->buffer->clearBuffer();
        }
        if ($message && $showTitle) {
            $message = "\n\r" . 'PHPJet Engine Shutdown Message: ' . $message . "\n\r";
        }
        exit($message);
    }

    /**
     * @param Router $router
     * @return string
     * @deprecated
     */
    private function execute(Router $router): string
    {
        return $router->start();
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
