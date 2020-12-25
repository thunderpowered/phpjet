<?php


namespace CloudStore\App\MVC\Client\Models;


use CloudStore\App\Engine\Core\Model;
use CloudStore\CloudStore;

/**
 * Class ModelMods
 * @package CloudStore\App\MVC\Client\Models
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

    /**
     * @return array
     */
    public function getModsBest(): array
    {

    }

    /**
     * @param string $group
     * @param string $orderBy
     * @param string $orderHow
     * @return array
     */
    public function getMods(string $group, string $orderBy, string $orderHow): array
    {
        $groupDays = $this->groupDaysDefault;
        if (isset($this->groupDays[$group])) {
            $groupDays = $this->groupDays[$group];
        }

        $sql = "
            select id, games_id, users_id, name, url, rating, truncate(rating, 1) as rating_display, since, reviews, datediff(now(), since) div {$groupDays} as date_order from mods 
            left join
                (
                    select COUNT(*) as reviews, AVG(rating) as rating, item_id, item_table from reviews group by item_id, item_table
                ) as rating 
            on mods.id = rating.item_id and rating.item_table = 'mods'
            order by date_order, {$orderBy} {$orderHow}, reviews desc, name desc";

        $result = CloudStore::$app->store->execGet($sql);
        if (!$result) {
            return [];
        }

        $result = $this->convertRowsToGroupedArray($result, 'date_order');
        return $result;
    }

    /**
     * @param array $rows
     * @param string $groupKey
     * @param bool $unsetInitialKey
     * @return array
     */
    private function convertRowsToGroupedArray(array $rows, string $groupKey, bool $unsetInitialKey = false): array
    {
        $result = [];
        foreach ($rows as $key => $value) {
            if (!isset($result[$value[$groupKey]])) {
                $result[$value[$groupKey]] = [];
            }

            if ($unsetInitialKey) {
                unset($value[$groupKey]);
            }

            $result[$value[$groupKey]][] = $value;
        }
        return $result;
    }
}