<?php


namespace CloudStore\App\MVC\Client\Models;


use CloudStore\App\Engine\Core\Model;
use CloudStore\CloudStore;

/**
 * Class ModelMods
 * @package CloudStore\App\MVC\Client\Models
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
        $result = CloudStore::$app->tool->cache->getCache(__CLASS__, __FUNCTION__);
        if ($result) {
            return json_decode($result, true);
        }

        $result = $this->getMods('month', 'rating', 'desc');
        if ($result) {
            $result = array_shift($result);
        }

        CloudStore::$app->tool->cache->setCache(__CLASS__, __FUNCTION__, json_encode($result));
        return $result;
    }
}