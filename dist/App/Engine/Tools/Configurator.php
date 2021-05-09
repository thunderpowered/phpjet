<?php

namespace Jet\App\Engine\Tools;

use Jet\App\Engine\Components\ActiveRecord\Table;
use Jet\App\Engine\Components\ActiveRecord\Utils\_TableStatus;
use Jet\App\Engine\Components\ActiveRecord\Utils\Builder;
use Jet\App\Engine\Components\ActiveRecord\Utils\Manager;
use Jet\App\Engine\Config\Config;
use Jet\App\Engine\Config\Docs;
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
//        set_exception_handler([$this, 'exceptionHandler']);
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
        // disabled for production mode
        if (!$this->debug) {
            throw new CoreException('Migrations are disabled for production mode. See more information here: ' . Docs::returnDocLink('configure', 'migrations'));
        }

        $manager = new Manager(true);
        $save = in_array('--save', $argv);
        if (in_array('--hard', $argv)) {
            $result = $manager->migrate(Manager::MIGRATE_MODE_HARD, $save, in_array('--sys', $argv));
        } else if (in_array('--soft', $argv)) {
            $result = $manager->migrate(Manager::MIGRATE_MODE_SOFT, $save, in_array('--sys', $argv));
        } else {
            throw new CoreException("Invalid migration mode. Most likely you forgot to include --soft or --hard flags. See more info here: " . Docs::returnDocLink('configure', 'migrations'));
        }

        if ($result) {
            return "Migration completed";
        } else {
            // maybe add a bit more information?
            return "Migration failed";
        }
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
     * @param array $argv
     * @return string
     */
    public function views(array $argv = []): string
    {
        // todo
        return '';
    }

    /**
     * @param array $argv
     * @param array $params
     * @return array
     */
    private function proceedArgvParams(array $argv, array $params = []): array
    {
        // todo
        return [];
    }
}