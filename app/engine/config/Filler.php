<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 10.09.2019
 * Time: 22:58
 */

namespace CloudStore\App\Engine\Config;

/**
 * Class Filler
 * @package CloudStore\App\Engine\Config
 */
class Filler
{
    public function prepareConfig(): void
    {
        $db = database::getInstance();
        $this->setKey($db);
    }

    /**
     * @param $db
     */
    private function setKey(\PDO $db): void
    {

    }
}