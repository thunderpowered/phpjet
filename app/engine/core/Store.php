<?php

namespace CloudStore\App\Engine\Core;

use CloudStore\CloudStore;

/**
 * Class Store
 * @package CloudStore\App\Engine\Core
 *
 */
class Store
{

    /**
     * @var array
     */
    private $tables = [];
    /**
     * @var array
     */
    private $fields = [];
    /**
     * @var \PDO
     */
    private $db;
    /**
     * @var
     */
    private $timestamp;
    /**
     * @var array
     */
    private $types = [
        /* INTEGER */

        "tinyint" => 0,
        "smallint" => 0,
        "mediumint" => 0,
        "int" => 0,
        "bigint" => 0,
        /* FLOAT */
        "decimal" => 0,
        "float" => 0,
        "double" => 0,
        "real" => 0,
        /* OTHER NUMBER */
        "bit" => 0,
        "boolean" => 0,
        "serial" => 0,
        /* STRINGS */
        "char" => "",
        "varchar" => "",
        "tinytext" => "",
        "mediumtext" => "",
        "text" => "",
        "longtext" => "",
        "binary" => "",
        "varbinary" => "",
        "tinyblob" => "",
        "mediumblob" => "",
        "blob" => "",
        "longblob" => "",
        "enum" => "",
        "set" => ""
    ];
    /**
     * @var array
     * supported join types
     */
    private $joinTypes = [
        'LEFT',
        'LEFT OUTER',
        'RIGHT',
        'RIGHT OUTER',
        'INNER'
    ];

    /**
     *  it's just NULL
     */
    private $null = "NULL";

    /**
     * @var int
     */
    private $counter = 0;
    /**
     * @var string
     */
    private $queries = "";

    /* SELECT FROM DATABASE */
    /**
     * @var string
     */
    private $postfix = "_view";

    public function __construct()
    {
    }

    /**
     * @param string $table
     * @param array $condition
     * @param bool $removeSpecialChars
     * @param bool $linked
     * @return bool
     */
    public function loadOne(string $table, array $condition = array(), bool $removeSpecialChars = true, $linked = true): array
    {
        $result = $this->load($table, $condition, array(), array(), $removeSpecialChars, $linked);
        if ($result) {
            return $result[0];
        }
        return array();
    }

    /**
     * @param string $table
     * @param array $condition
     * @param array $orderBy
     * @param array $limit
     * @param bool $removeSpecialChars
     * @param bool $linked
     * @return array
     * @deprecated
     */
    public function load(string $table, array $condition = array(), array $orderBy = array(), array $limit = array(), bool $removeSpecialChars = true, $linked = true): array
    {
        $sql = $this->drawSelect($table, $condition, $orderBy, $limit);
        if (empty($sql)) {
            return array();
        }

        if ($condition) {
            $value = $this->makeValue($condition);
            return $this->execGet($sql, $value, $removeSpecialChars);
        } else {
            return $this->execGet($sql, array(), $removeSpecialChars);
        }
    }

    /**
     * @param string $table
     * @param array $join
     * @param array $condition
     * @param array $orderBy
     * @param array $limit
     * @param bool $removeSpecialChars
     * @return array
     */
    public function loadNew(string $table, array $join = array(), array $condition = array(), array $orderBy = array(), array $limit = array(), bool $removeSpecialChars = true): array
    {
        $sql = $this->drawSelectNew($table, $join, $condition, $orderBy, $limit);
        if (!$sql) {
            return [];
        }

        $value = $this->makeValue($condition);

        if ($condition) {
            $value = $this->makeValue($condition);
            return $this->execGet($sql, $value, $removeSpecialChars);
        } else {
            return $this->execGet($sql, array(), $removeSpecialChars);
        }
    }

    /**
     * @param string $sql
     * @param array $params
     * @param bool $removeSpecialChars
     * @return array
     */
    public function execGet(string $sql, array $params = array(), bool $removeSpecialChars = true): array
    {
        if (!$params) {
            $stmt = $this->db->query($sql);
            if (!$stmt) {
                return array();
            }
            $result = $stmt->fetchAll();
            if (!$result) {
                return array();
            }
        } else {
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($params);
            if (!$result) {
                return array();
            }

            $result = $stmt->fetchAll();
            if (!$result) {
                return array();
            }
        }

        // dev
        $this->counter++;
        $this->queries .= "\n" . $sql;

        if ($removeSpecialChars) {
            $result = $this->removeSpecialChars($result);
        }
        return $result;
    }

    /* INSERT INTO DATABASE */

    /**
     * @param string $table
     * @param array $condition
     * @param bool $linked
     * @return bool
     */
    public function collect(string $table, array $condition = array(), $linked = true): bool
    {
        if (empty($condition)) {
            return false;
        }

        $condition = $this->prepareCondition($table, $condition);
        $sql = $this->drawInsert($table, $condition);
        if (empty($sql)) {
            return false;
        }

        $value = $this->makeValue($condition);
        $params = $this->setEmpty($table, $value);

        return $this->execSet($sql, $params);
    }

    /**
     * @param string $sql
     * @param array $params
     * @return bool
     */
    public function execSet(string $sql, array $params = array()): bool
    {
        if (empty($params) OR empty($sql) OR !is_string($sql)) {

            return false;
        }

        $this->counter++;
        $this->queries .= "\n" . $sql;

        try {

            return $this->db->prepare($sql)->execute($params);
        } catch (\Exception $e) {

            // Just return false, it means that something not good happened
            return false;
        }
    }

    /* UPDATE */

    /**
     * @param string $table
     * @param array $fields
     * @param array $condition
     * @param bool $linked
     * @return bool
     */
    public function update(string $table, array $fields = array(), array $condition = array(), $linked = true): bool
    {
        if (empty($fields) OR empty($condition)) {

            return false;
        }

        $sql = $this->drawUpdate($table, $fields, $condition);

        if (empty($sql)) {

            return false;
        }

        $value = array_merge($this->makeValue($condition), $this->makeValue($fields, "set"));

        // Be careful!
        $value = $this->setEmpty($table, $value);
        //

        return $this->execSet($sql, $value);
    }

    /* DELETE */

    /**
     * @param string $table
     * @param array $condition
     * @param bool $linked
     * @return bool
     */
    public function delete(string $table, array $condition = array(), $linked = true): bool
    {
        if (empty($table) OR empty($condition)) {

            return false;
        }

        $sql = $this->drawDelete($table, $condition);

        if (empty($sql)) {

            return false;
        }

        $value = $this->makeValue($condition);

        return $this->execSet($sql, $value);
    }

    /* COUNT */

    /**
     * @param string $table
     * @param array $condition
     * @return int
     */
    public function count(string $table, array $condition = array()): int
    {
        $sql = $this->drawSelect($table, $condition, array(), array(), true);

        if ($condition) {

            $value = $this->makeValue($condition);

            return (int)$this->execGet($sql, $value)[0]["COUNT"];
        } else {

            return (int)$this->execGet($sql)[0]["COUNT"];
        }
    }

    /* DEBUG PURPOSE ONLY */

    /**
     * @return int
     */
    public function getNumberOfQueries(): int
    {
        return $this->counter;
    }

    /**
     * @return int
     */
    public function getQueries(): int
    {
        return $this->queries;
    }

    /**
     * @param \PDO $db
     */
    public function setDB(\PDO $db)
    {
        $this->db = $db;
        $this->setDate();
    }

    /**
     * @param array $tables
     */
    public function setTables(array $tables)
    {
        $this->tables = $tables;
    }

    private function setDate()
    {

        if (!$this->timestamp) {

            $this->timestamp = time();
        }

        $date = date("Y-m-d", $this->timestamp);
        $time = date("H:i:s", $this->timestamp);
        $year = substr($date, 0, 4);

        $this->types['date'] = $date;
        $this->types['datetime'] = $date . ' ' . $time;
        $this->types['timestamp'] = $date . ' ' . $time;
        $this->types['time'] = $time;
        $this->types['year'] = $year;
    }

    /**
     * @return string
     */
    public function now(): string
    {

        if (!$this->timestamp) {

            $this->timestamp = time();
        }

        $date = date("Y-m-d", $this->timestamp);
        $time = date("H:i:s", $this->timestamp);

        return $date . ' ' . $time;
    }

    /**
     * @return array
     */
    private function getTables(): array
    {
        return $this->tables;
    }

    /**
     * @param string $table
     * @param array $param
     * @return array
     */
    private function prepareCondition(string $table, array $param): array
    {

        if (!in_array($table, $this->fields)) {

            $this->setFields($table);
        }

        foreach ($this->fields[$table] as $key => $value) {

            if (!array_key_exists($key, $param)) {

                $param[$key] = $value;
            }
        }

        return $param;
    }

    /**
     * @param string $table
     */
    private function setFields(string $table)
    {
        if (isset($this->fields[$table])) {
            return;
        }

        $stmt = $this->db->prepare("SHOW FIELDS FROM " . $this->prepareTable($table));
        $stmt->execute();

        $temp = $stmt->fetchAll();

        foreach ($temp as $key => $value) {
            if ($value['Field'] === "id") {
//                continue;
            }

            $type = strtolower($value['Type']);
            if (($end = strpos($type, '(')) !== false) {
                $type = substr($type, 0, $end);
            }
            $empty = array_key_exists($type, $this->types) ? $this->types[$type] : null;
            $this->fields[$table][$value['Field']] = $empty;
        }
    }

    /**
     * @param string $table
     * @return string
     */
    private function prepareTable(string $table): string
    {
        if (!in_array($table, $this->tables)) {
            CloudStore::$app->exit("Table $table doesn't exist.");
        }

        return $table . $this->postfix;
    }

    /**
     * @param string $table
     * @param array $param
     * @return array
     */
    private function setEmpty(string $table, array $param): array
    {
        if (!in_array($table, $this->fields)) {
            $this->setFields($table);
        }

        foreach ($param as $key => $value) {
            if ($value === $this->null) {
                $param[$key] = null;
                continue;
            }
            if (empty($value)) {
                $key_clean = substr($key, 1);
                if (strpos($key_clean, "set_") !== false) {
                    $key_clean = substr($key_clean, 4);
                }

                $param[$key] = array_key_exists($key_clean, $this->fields[$table]) ? $this->fields[$table][$key_clean] : null;
            }
        }

        return $param;
    }

    /**
     * @param array $condition
     * @param string|null $prefix
     * @return array
     */
    private function makeValue(array $condition, string $prefix = null): array
    {
        if (empty($condition)) {
            return array();
        }

        $value = array();
        foreach ($condition as $field => $item) {
            if ($prefix) {
                $prefix = $prefix . "_";
            }

            if (is_array($item)) {
                foreach ($item as $key => $_item) {
                    $value[":" . $prefix . $this->escape($field) . $key] = $_item;
                }
            } else {
                if (strpos($item, "!") === 0) {
                    $item = substr($item, 1);
                }
                $value[":" . $prefix . $this->escape($field)] = $item;
            }
        }

        return $value;
    }

    /**
     * @param $string
     * @return string
     */
    private function escape($string): string
    {
        return str_replace('\'', '', $this->db->quote($string));
    }

    /**
     * @param string $table
     * @param array $condition
     * @return string
     */
    private function drawDelete(string $table, array $condition): string
    {
        $table = $this->prepareTable($table);
        return "DELETE FROM " . $table . " WHERE " . $this->drawWhere($condition, $table);
    }

    /**
     * @param array $array
     * @return string
     */
    private function drawWhere(array $array, string $table): string
    {
        $result = '';
        $clause = '';
        foreach ($array as $field => $value) {
            if (is_array($value)) {
                // use IN (?)
                $placeholders = '';
                foreach ($value as $key => $item) {
                    $placeholders .= ',:' . $this->escape($field) . $key;
                }
                $placeholders = substr($placeholders, 1);
                $result .= $table . '.' . $this->escape($field) . ' IN (' . $placeholders . ')';
            } else {
                // regular where
                if ($result) {
                    $clause = strpos($field, "!") === 0 ? " OR " : " AND ";
                }
                $operator = strpos($value, "!") === 0 ? "<>" : "=";
                $result .= $clause . $table . '.' . $this->escape($field) . " " . $operator . " :" . $this->escape($field);
            }
        }

        return $result;
    }

    /**
     * @param array $conditions
     * @param string $joinTable
     * @param $mainTable
     * @return string
     */
    private function drawOn(array $conditions, string $joinTable, $mainTable): string
    {
        $result = [];
        foreach ($conditions as $joinField => $mainField) {
            $result[] = $joinTable . '.' . $joinField . ' = ' . $mainTable . '.' . $mainField;
        }

        return implode(' AND ', $result);
    }

    /**
     * @param string $table
     * @param array $fields
     * @param array $condition
     * @return string
     */
    private function drawUpdate(string $table, array $fields, array $condition): string
    {
        $table = $this->prepareTable($table);
        return "UPDATE " . $table . " SET " . $this->drawValues($fields, "update") . " WHERE " . $this->drawWhere($condition, $table);
    }

    /**
     * @param array $condition
     * @param string $type
     * @return array|bool|string
     */
    private function drawValues(array $condition, string $type)
    {
        if ($type === "insert") {
            $fields = implode(', ', array_keys($condition));
            $values = ':' . implode(', :', array_keys($condition));

            return [
                'fields' => $fields,
                'values' => $values
            ];
        } else if ($type === "update") {

            $eq = null;
            $sql = "";

            foreach ($condition as $key => $value) {
                $sql .= $eq . $this->escape($key) . " = :set_" . $this->escape($key);
                $eq = ", ";
            }

            return $sql;
        }

        return false;
    }

    /**
     * @param string $table
     * @param array $condition
     * @return string
     */
    private function drawInsert(string $table, array $condition): string
    {
        $data = $this->drawValues($condition, "insert");
        return "INSERT INTO " . $this->prepareTable($table) . " ( " . $data['fields'] . " ) VALUES ( " . $data['values'] . " )";
    }

    /**
     * @param string $table
     * @param array $condition
     * @param array $orderBy
     * @param array $limit
     * @param bool $count
     * @param bool $join
     * @return string
     * @deprecated
     */
    private function drawSelect(string $table, array $condition, array $orderBy, array $limit, bool $count = false, array $join = array()): string
    {
        $table = $this->prepareTable($table);
        $orderBy = $this->drawOrderBy($orderBy, $table);
        $limit = $this->drawLimit($limit);
        $join = $this->drawJoin($join, $table);

        return "SELECT " . ($count ? "COUNT(*) AS COUNT" : "*") . " FROM " . $table . $join . ($condition ? " WHERE " . $this->drawWhere($condition, $table) : "") . $orderBy . $limit;
    }

    private function drawSelectNew(string $table, array $join, array $condition, array $orderBy, array $limit, bool $count = false): string
    {
        $what = $this->drawWhat($table, $join);
        $table = $this->prepareTable($table);
        $orderBy = $this->drawOrderBy($orderBy, $table);
        $limit = $this->drawLimit($limit);
        $join = $this->drawJoin($join, $table);
        $condition = $this->drawWhere($condition, $table);

        return "SELECT " . ($count ? "COUNT(*) AS COUNT" : $what) . " FROM " . $table . $join . ($condition ? " WHERE " . $condition : "") . $orderBy . $limit;
    }

    /**
     * @param string $table
     * @param array $join
     * @return string
     */
    private function drawWhat(string $table, array $join): string
    {
        $this->setFields($table);
        $fields = $this->fields[$table];
        $table = $this->prepareTable($table);
        $result = "";
        $delimiter = ", ";

        // main table
        foreach ($fields as $field => $value) {
            $result .= $table . '.' . $field . $delimiter;
        }
        // join tables
        foreach ($join as $condition) {
            $joinTable = $condition[1];
            $joinTablePrepared = $this->prepareTable($joinTable);
            $this->setFields($joinTable);
            $joinFields = $this->fields[$joinTable];
            foreach ($joinFields as $field => $value) {
                if (array_key_exists($field, $fields)) {
                    $field = $field . ' AS ' . $joinTable . '_' . $field;
                }
                $result .= $joinTablePrepared . '.' .$field . $delimiter;
            }
            $fields = array_merge($fields, $joinFields);
        }

        return rtrim($result, $delimiter);
    }

    /**
     * @param array $join
     * @return string
     */
    private function drawJoin(array $join, string $table): string
    {
        $result = "";
        $delimiter = " ";
        foreach ($join as $key => $condition) {
            $joinType = strtoupper($condition[0]);
            $joinTable = $this->prepareTable($condition[1]);
            $joinCondition = $this->drawOn($condition[2], $joinTable, $table);

            if (!in_array($joinType, $this->joinTypes)) {
                continue;
            }
            $result .= $joinType . " JOIN " . $joinTable . " ON " . $joinCondition . $delimiter;
        }

        $result = rtrim($result, " ");
        if ($result) {
            $result = " " . $result;
        }

        return $result;
    }

    /**
     * @param array $orderBy
     * @return string
     */
    private function drawOrderBy(array $orderBy, string $table): string
    {
        $result = "";
        $delimiter = ", ";
        foreach ($orderBy as $field => $key) {
            $result .= $table . '.' . $this->escape($field) . " " . strtoupper($this->escape($key)) . $delimiter;
        }

        $result = rtrim($result, ", ");
        if ($result) {
            $result = " ORDER BY " . $result;
        }
        return $result;
    }

    /**
     * @param array $limit
     * @return string
     */
    private function drawLimit(array $limit): string
    {
        if (empty($limit)) {
            return "";
        }

        if (count($limit) !== 2) {
            return "";
        }

        return " LIMIT " . $this->escape($limit[0]) . (!empty($limit[1]) ? ", " . $this->escape($limit[1]) : "");
    }

    /**
     * @param $result
     * @return array|string
     */
    private function removeSpecialChars($result)
    {
        if (!is_array($result) AND !is_object($result)) {
            return CloudStore::$app->tool->utils->removeSpecialChars($result);
        }

        for ($i = 0; $i < count($result); $i++) {
            foreach ($result[$i] as $key => $value) {
                $result[$i][$key] = CloudStore::$app->tool->utils->removeSpecialChars($value);
            }
        }

        return $result;
    }
}
