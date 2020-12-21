<?php

namespace CloudStore\App\Engine\Tools;

use CloudStore\App\Engine\Config\Database;
use CloudStore\App\Engine\Core\Component;

/**
 *
 * Getter class is deprecated solution
 * This class must be removed!
 * @todo remove it and change all methods using it
 *
 *
 * Component: ShopEngine Getter
 * Description: Methods for getting data from DataBase (use MySQL).
 *
 * Instructions:
 *
 * Use \CloudStore\App\Engine\Tools\Getter::getFreeData( string $sql, array $params, bool $type, bool $html ); to get data from MySQL.
 * $sql - your SQL-query string Ex. "SELECT * FROM products WHERE id = ?".
 * $params - array of params. Count of params must be equal to ? symbol in $sql.
 * $type:
 *     true - return first of element of array. Use it if you need to get single value.
 *     false - return all elements of array.
 * $html - strip html tags (true - strip, false - don't)
 * Use \CloudStore\App\Engine\Tools\Getter::getFreeProducts( string $sql, array $params, bool $type ) to get products from custom SQL-query without pagination and some additional actions. It uses products_customizer trait.
 * $sql - your SQL-query string Ex. "SELECT * FROM products WHERE id = ?".
 * $params - array of params. Count of params must be equal to ? symbol in $sql.
 * $type:
 *     true - return first of element of array. Use it if you need to get single value.
 *     false - return all elements of array.
 * Use \CloudStore\App\Engine\Tools\Getter::getProducts( string $sql, array $params, $num, $main ) to get products from SQL-query. Be careful, your query-string must be valid.
 * $sql - your SQL-query string Ex. "SELECT * FROM products WHERE id = ?".
 * $params - array of params. Count of params must be equal to ? symbol in $sql.
 * $num - number pf products per page.
 * $main - use it if you want to show products with enabled "main" option
 *
 * You don't have to use ORDER BY and LIMIT in query-string because this words need to pagination and sorting.
 * Invalid query-string can be cause of critical error. All information about error you can find in errlog.txt file.
 * Use \CloudStore\App\Engine\Tools\Getter::getFreeProducts if you don't want to bother.
 * Use \CloudStore\App\Engine\Tools\Getter::getDataWithPagination() to get free data with enagling pagination. You can use ORDER BY,but you still need to avoid LIMIT.
 * Parameters equal to getProducts method (without $main)
 *
 */
class Getter extends Component
{

    public static $queries;
    public static $count;

    private static $sql;
    private static $params;
    private static $num;

    public static function getDataWithPagination($sql, array $params = NULL, $num = 20)
    {

        // The last working method. Will be remove with the entire class.
        // Solution copied from Products::loadExec();

        $count = count(S::execGet($sql, $params));

        if (!$count) {

            return false;
        }

        $array = Paginator::preparePagination($count, $num);

        $query_start_num = $array["limit_str"];

        if ($num) {

            $products = CloudStore::$app->store->execGet($sql . " " . $query_start_num, $params, false);
        } else {

            $products = CloudStore::$app->store->execGet($sql, $params);
        }

        return $products;

        // Deprecated
        Getter::$queries .= '<li>Query: ' . $sql . '</li>';
        Getter::$count++;

        Getter::$sql = $sql;
        Getter::$params = $params;
        Getter::$num = $num;

        $db = Database::getInstance();

        $array = Paginator::preparePagination($sql, $params, $num);
        $sorting_db = $array["qsort"];
        $query_start_num = $array["query"];

        // With params or without
        if ($params !== NULL) {

            $query = $db->prepare($sql . $query_start_num);
            $query->execute($params);
        } else {

            $query = $db->query($sql . $query_start_num);
        }

        $array = $query->fetchAll();

        for ($i = 0; $i < count($array); $i++) {

            foreach ($array[$i] as $key => $value) {

                $array[$i][$key] = htmlentities($value);
            }
        }

        return $array;
    }
}
