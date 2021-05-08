<?php


namespace Jet\App\Engine\Interfaces;

/**
 * Class ChainSection
 * @package Jet\App\Engine\Interfaces
 */
class ChainSection
{
    /**
     * @var array
     */
    public $requirements;

    /**
     * @return ViewResponse
     */
    public function next(): ViewResponse
    {
        return new ViewResponse();
    }
}