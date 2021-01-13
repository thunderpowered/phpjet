<?php


namespace Jet\App\MVC\Client\Models;


use Jet\App\Engine\Core\Model;
use Jet\PHPJet;

/**
 * Class ModelItems
 * @package Jet\App\MVC\Client\Models
 */
class ModelItems extends Model
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
     * @var int
     */
    private $differenceToNew = 86400;

    /**
     * ModelItems constructor.
     * @param string $name
     */
    public function __construct(string $name = "")
    {
        parent::__construct($name);
    }

    /**
     * @param string $group
     * @param string $orderBy
     * @param string $orderHow
     * @param int $limit
     * @return array
     */
    public function getItemsGroupedByDateWithASingleParent(string $group, string $orderBy, string $orderHow, int $limit = 20): array
    {
        $cacheIdentifier = __FUNCTION__ . $group . $orderBy . $orderHow . $$limit;
        $result = PHPJet::$app->tool->cache->getCache(__CLASS__, $cacheIdentifier);
        if ($result) {
            return json_decode($result, true);
        }

        $groupDays = $this->groupDaysDefault;
        if (isset($this->groupDays[$group])) {
            $groupDays = $this->groupDays[$group];
        }

        $store = PHPJet::$app->store;

        $sql = "
            select i.id, i.name as item_name, i.url as item_url, i.users_id, i.icon, r.rating, truncate(r.rating, 1) as rating_display, r.reviews, datediff(now(), i.since) div {$groupDays} as date_order, p.name as parent_name, p.url parent_url from {$store->prepareTable('items')} i 
            left join
                (
                    select COUNT(*) as reviews, AVG(rating) as rating, items_id from {$store->prepareTable('reviews')} group by items_id
                ) r on i.id = r.items_id
            left join 
                {$store->prepareTable('items')} p on i.parent = p.id
            order by date_order, r.{$orderBy} {$orderHow}, r.reviews desc, i.name desc limit 0, {$limit}";

        $result = $store->execGet($sql);
        if (!$result) {
            return [];
        }

        foreach ($result as $key => $item) {
            if ($item['icon']) {
                $result[$key]['icon'] = PHPJet::$app->tool->utils->getThumbnailLink($item['icon']);
            }
            $result[$key]['item_url'] = $this->getItemFullURL($item['item_url']);
            $result[$key]['parent_url'] = $this->getItemFullURL($item['parent_url']);
        }

        $result = $this->convertRowsToGroupedArray($result, 'date_order');
        PHPJet::$app->tool->cache->setCache(__CLASS__, $cacheIdentifier, json_encode($result));
        return $result;
    }

    /**
     * @param string $itemTime
     * @return bool
     */
    public function isThisItemNew(string $itemTime): bool
    {
        $currentTime = time();
        $itemTime = strtotime($itemTime);
        $timeDifference = $currentTime - $itemTime;
        return $timeDifference <= $this->differenceToNew;
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

    /**
     * @param string $url
     * @return string
     */
    public function getItemFullURL(string $url): string
    {
        return PHPJet::$app->router->getHost() . '/' . $url;
    }
}