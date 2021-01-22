<?php


namespace Jet\App\MVC\Client\Models;


use Jet\App\Engine\Core\Model;
use Jet\PHPJet;

/**
 * Class ModelMods
 * @package Jet\App\MVC\Client\Models
 * @deprecated
 */
class ModelMods extends Model
{
    /**
     * @var array
     */
    private $groupDays = [
        'day' => 1,
        'week' => 7,
        'month' => 30,
        'half-year' => 180,
        'year' => 365
    ];
    /**
     * @var int
     */
    private $groupDaysDefault = 30;
    /**
     * ModelMods constructor.
     * @param string $name
     */
    public function __construct(string $name = "")
    {
        parent::__construct($name);
    }

    /**
     * @return array
     */
    public function getModsLastMonth(): array
    {
        $result = PHPJet::$app->tool->cache->getCache(__CLASS__, __FUNCTION__);
        if ($result) {
            return json_decode($result, true);
        }

        $result = $this->getMods('month', 'rating', 'desc');
        if ($result) {
            $result = array_shift($result);
        }

        PHPJet::$app->tool->cache->setCache(__CLASS__, __FUNCTION__, json_encode($result));
        return $result;
    }
}