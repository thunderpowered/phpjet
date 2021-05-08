<?php

namespace Jet\App;

use Jet\App\Engine\Config\Config;
use Jet\App\Engine\Config\ConfigManager;
use Jet\App\Engine\Config\Database;
use Jet\App\Engine\Core\Router;
use Jet\App\Engine\Core\Selector;
use Jet\App\Engine\Core\Store;
use Jet\App\Engine\Core\System;
use Jet\App\Engine\Core\Tool;
use Jet\App\Engine\Exceptions\CoreException;
use Jet\App\Engine\System\Error;
use Jet\App\Engine\System\PageBuilder;
use Jet\PHPJet;

/**
 * Class App
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

        // Select the config (deprecated, since for now all the information about different apps is in database)
        // So there's no need to manage specific config selection
        // todo just remove config selection
        $configSelector = new Selector();
        $configSelector->select();

        // load components
        $this->store = new Store(Engine\Config\Config::$dev['debug']);
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

        $this->error = new Error();
    }

    /**
     * @return string
     */
    public function start(): string
    {
        // prepare class Store
        Database::setConfig(Config::$db);
        $this->store->setDB(Database::getInstance(), Config::$db);
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
            && substr($functionName, 0, 1) !== '_' // old specific solution, it can be removed
            && method_exists($this->tool->configurator, $functionName)) {
            Database::setConfig(Config::$db);
            $this->store->setDB(Database::getInstance(), Config::$db);
            $this->store->prepareTables();

            try {
                return $this->tool->configurator->$functionName($argv);
            } catch (CoreException $exception) {
                $this->exit($exception->getNotes());
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
        if (PHPJet::$app->system) { // can be null in configuration mode
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
        } else {
            trigger_error("File '$fileName' not found", E_USER_WARNING);
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
//        foreach ($path as $key => $value) {
//            $path[$key] = strtolower($value);
//        }

        // as i mentioned before, first part of namespace should contain root namespace name
        $first = $path[0];
//        $nameSpaceRoot = strtolower(NAMESPACE_ROOT);
        if ($first !== NAMESPACE_ROOT) {
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
