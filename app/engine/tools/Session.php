<?php

namespace CloudStore\App\Engine\Tools;


use CloudStore\App\Engine\Core\Component;

/**
 * Class Session
 * @package CloudStore\App\Engine\Tools
 */
class Session extends Component
{
    /**
     * @param string $name
     */
    public function get(string $name = "")
    {

    }

    /**
     * @param array $session
     * @return bool
     */
    public function set(array $session = array()): bool
    {

        return true;
    }
}