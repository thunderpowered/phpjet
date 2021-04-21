<?php

namespace Jet\App\Engine\Tools;

/**
 * Class Configurator
 */
class Configurator
{
    /**
     * @var array
     */
    private $argv;

    /**
     * Configurator constructor.
     * @param array $argv
     */
    public function __construct(array $argv = [])
    {
        $this->argv = $argv;
    }

    /**
     * @return string
     */
    public function migrate()
    {
        return 'yes, i am a migrate function';
    }
}