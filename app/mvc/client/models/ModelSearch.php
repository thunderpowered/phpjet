<?php


namespace CloudStore\App\MVC\Client\Models;


use CloudStore\App\Engine\Core\Model;
use CloudStore\CloudStore;

/**
 * Class ModelSearch
 * @package CloudStore\App\MVC\Client\Models
 */
class ModelSearch extends Model
{
    /**
     * @var int
     */
    private $maxSearchValueString = 64;
    private $typeRU = [
        'games' => 'Игра',
        'mods' => 'Мод',
        'users' => 'Пользователь'
    ];
    /**
     * ModelSearch constructor.
     * @param string $name
     */
    public function __construct(string $name = "")
    {
        parent::__construct($name);
    }

    /**
     * @param string $searchValue
     * @param int $limit
     * @return array
     */
    public function quickSearch(string $searchValue, int $limit = 10): array
    {
        $searchValueLength = strlen($searchValue);
        if (!$searchValueLength || $searchValueLength > $this->maxSearchValueString) {
            return [];
        }

        // check cache
//        $cacheIdentifier = CloudStore::$app->tool->formatter->anyStringToURLString($searchValue);
        $cacheIdentifier = mb_strtolower(trim($searchValue));
        $cache = CloudStore::$app->tool->cache->getCache(__FUNCTION__, $cacheIdentifier);
        if ($cache) {
            return json_decode($cache, true);
        }

        $result =  $this->searchInAllTables2($searchValue, $limit);
        foreach ($result as $key => $value) {
            $result[$key]['typeRU'] = $this->typeRU[$value['type']];
            if ($value['icon']) {
                $result[$key]['icon'] = CloudStore::$app->tool->utils->getThumbnailLink($value['icon']);
            }
        }

        // save into cache
        CloudStore::$app->tool->cache->setCache(__FUNCTION__, $cacheIdentifier, json_encode($result));
        return $result;
    }

    /**
     * @param string $searchValue
     * @param int $limit
     * @return array
     */
    public function searchInAllTables(string $searchValue, int $limit = 10): array
    {
        // @todo
        return [];
    }

    /**
     * @param string $searchValue
     * @param int $limit
     * @return array
     */
    public function searchInAllTables2(string $searchValue, int $limit = 10): array
    {
        // trim and remove chars to make search easier
        $searchValue = CloudStore::$app->tool->formatter->anyStringToSearchString($searchValue);
        $searchArray = explode(' ', $searchValue);

        $gamesSQL  = '';
        $modsSQL   = '';
        $delimiter = '';
        $parameters = [];

        // initial idea was to create fuzzy search in multiple tables
        // to allow users to search among ALL site content
        // i came to this solution:
        // 1. We explode our string to get every single word from query string
        // 2. We search using every single word separately
        // 3. While searching we count how many times we found one particular row
        // 4. The number of "hits" is our relevance
        // 5. So we can wrap all this queries into sub query and order by our relevance
        // It works, actually, but works pretty slow i suppose
        // Let's see, maybe i'll find a better solution
        // Pretty sure i can use Levenshtein distance or something similar instead of this kinda trash, can't i? I'll try it a bit later
        // @todo think about algorithm
        foreach ($searchArray as $key => $value) {
            if ($key) {
                $delimiter = "union all";
            }

            $placeHolderGames = ':placeholderGames' . $key;
            $gamesSQL .= $delimiter . " select id, 'games' as type, url, name, icon from games where name like CONCAT('%', {$placeHolderGames}, '%')";
            $parameters[$placeHolderGames] = $value;

            $placeHolderMods = ':placeholderMods' . $key;
            $modsSQL .= $delimiter . " select id, 'mods' as type, url, name from mods where name like CONCAT('%', {$placeHolderMods}, '%')";
            $parameters[$placeHolderMods] = $value;
        }

        $sql = "select * from 
                    (
                            select count(*) as relevance, id, name, url, type, icon from 
                            (
                                {$gamesSQL}
                            ) as games group by id, name, url, type, icon having relevance = :relevance1
                            union all
                            select count(*) as relevance, id, name, url, type, '' as icon from 
                            (
                                {$modsSQL}
                            ) as mods group by id, name, url, type having relevance = :relevance2
                    ) as subquery order by relevance DESC, name DESC";

        // we show the result only if the search was successful for all words
        // i need to prevent queries like 'Star Wars s',
        // because you know a lot of staff could be found by word s, we don't need it
        $requiredRelevance = count($searchArray);
        $parameters[":relevance1"] = $requiredRelevance;
        $parameters[":relevance2"] = $requiredRelevance;

        $result = CloudStore::$app->store->execGet($sql, $parameters);

        return (array)$result;
    }
}