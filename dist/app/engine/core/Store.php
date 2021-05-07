<?php


namespace Jet\App\Engine\Core;

use Exception;
use Jet\App\Engine\Config\Config;
use Jet\App\Engine\Exceptions\CoreException;
use Jet\PHPJet;
use PDO;

/**
 * Class Store2
 * @package Jet\App\Engine\Core
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
    private $views = [];
    /**
     * @var array
     */
    private $triggers = [];
    /**
     * @var array
     */
    private $fields = [];
    /**
     * @var PDO
     */
    private $db;
    /**
     * @var string
     */
    private $dbname;
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
     * @var array
     */
    private $orderTypes = [
        'ASC', 'DESC'
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
    /**
     * @var string
     */
    private $triggerPostfix = '_trigger';
    /**
     * @var string
     */
    private $partitionColumnName = '_config_id';
    /**
     * @var string
     */
    private $partitionFunction = 'getConfigID';
    /**
     * @var bool
     * If true, everything that passes to functions load(), loadOne(), update(), collect(), delete() and count() will be whitelisted
     * If false, only tables will be whitelisted
     * Recommended to keep as true on production
     * Functions execGet() and execSet() will not be validated. It is up on developer.
     */
    private $validateEverything = true;
    /**
     * @var string
     */
    private $defaultErrorMessage = 'Database error';
    /**
     * @var string
     */
    private $dumpFileLocation = ROOT . 'backups/database/';

    /**
     * @return string
     */
    public function getLastInsertId(): string
    {
        return $this->db->lastInsertId();
    }

    /**
     * @return string
     */
    public function getPartitionColumnName(): string
    {
        return $this->partitionColumnName;
    }

    /**
     * @return int
     */
    public function getNumberOfQueries(): int
    {
        return $this->counter;
    }

    /**
     * @return int
     * @deprecated
     */
    public function getQueries(): int
    {
        return $this->queries;
    }

    /**
     * @param PDO $db
     * @param string $dbname
     */
    public function setDB(PDO $db, string $dbname)
    {
        $this->db = $db;
        $this->dbname = $dbname;
        $this->setDate();
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

    public function prepareTables()
    {
        $this->showTables();
        $this->showTriggers();
    }

    /**
     * @param string $SQLString
     * @param array $params
     * @param bool $removeSpecialChars
     * @return array
     */
    public function execGet(string $SQLString, array $params = [], bool $removeSpecialChars = true): array
    {
        $this->counter++;
        if ($params) {
            $PDOStatement = $this->db->prepare($SQLString);
            if (!$PDOStatement) {
                return [];
            }

            $executed = $PDOStatement->execute($params);
            if (!$executed) {
                return [];
            }
        } else {
            $PDOStatement = $this->db->query($SQLString);
            if (!$PDOStatement) {
                return [];
            }
        }

        $result = $PDOStatement->fetchAll();
        if (!$result) {
            return [];
        }

        if ($removeSpecialChars) {
            $result = $this->removeSpecialChars($result);
        }

        return $result;
    }

    /**
     * @param string $SQLString
     * @param array $params
     * @return bool
     */
    public function execSet(string $SQLString, array $params): bool
    {
        $this->counter++;
        $PDOStatement = $this->db->prepare($SQLString);
        if (!$PDOStatement) {
            return false;
        }

        // returns true or false anyway
        return $PDOStatement->execute($params);
    }

    /**
     * @param string $SQLString
     * @return bool
     */
    public function dangerouslySendQueryWithoutPreparation(string $SQLString): bool
    {
        $this->counter++;
        $result = $this->db->exec($SQLString);
        if ($result === false) {
            // exec returns number of string which were modified or deleted / or false
            // so if (result) is not valid, since if (0) is false anyway
            return false;
        }
        return true;
    }

    /**
     * @param string $table
     * @param array $condition
     * @param bool $removeSpecialChars
     * @return array
     * @throws Exception
     */
    public function loadOne(string $table, array $condition = [], bool $removeSpecialChars = true): array
    {
        $result = $this->load($table, $condition, [], [0, 1], $removeSpecialChars);
        if (!isset($result[0])) {
            return [];
        }
        return $result[0];
    }

    /**
     * @param string $table
     * @param array $condition
     * @param array $orderBy
     * @param array $limit
     * @param bool $removeSpecialChars
     * @return array
     * @throws Exception
     * @deprecated
     */
    public function load(string $table, array $condition = [], array $orderBy = [], array $limit = [], bool $removeSpecialChars = true): array
    {
        $SQLString = $this->drawSQLString($table, [], [], $condition, $orderBy, $limit, 'select', false, $this->validateEverything);

        if ($condition) {
            $values = $this->makeValues($condition);
            return $this->execGet($SQLString, $values, $removeSpecialChars);
        } else {
            return $this->execGet($SQLString, [], $removeSpecialChars);
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
     * @throws Exception
     */
    public function load2(string $table, array $join = array(), array $condition = array(), array $orderBy = array(), array $limit = array(), bool $removeSpecialChars = true): array
    {
        $SQLString = $this->drawSQLString($table, $join, [], $condition, $orderBy, $limit, 'select', false, $this->validateEverything);

        if ($condition) {
            $values = $this->makeValues($condition);
            return $this->execGet($SQLString, $values, $removeSpecialChars);
        } else {
            return $this->execGet($SQLString, [], $removeSpecialChars);
        }
    }

    /**
     * @param string $table
     * @param array $condition
     * @return bool
     * @throws Exception
     */
    public function collect(string $table, array $condition = []): bool
    {
        if (!$condition) {
            return false;
        }

        $SQLString = $this->drawSQLString($table, [], [], $condition, [], [], 'insert', false, $this->validateEverything);

        $values = $this->makeValues($condition);
        $params = $this->setEmptyFields($table, $values);
        return $this->execSet($SQLString, $params);
    }

    /**
     * @param string $table
     * @param array $fields
     * @param array $condition
     * @return bool
     * @throws Exception
     */
    public function update(string $table, array $fields, array $condition): bool
    {
        if (!$fields || !$condition) {
            return false;
        }

        $SQLString = $this->drawSQLString($table, [], $fields, $condition, [], [], 'update', false, $this->validateEverything);
        $valuesOfUpdateFields = $this->makeValues($fields, 'set');
        $valuesOfCondition = $this->makeValues($condition);
        $values = array_merge($valuesOfCondition, $valuesOfUpdateFields);
        $values = $this->setEmptyFields($table, $values);
        return $this->execSet($SQLString, $values);
    }

    /**
     * @param string $table
     * @param array $condition
     * @return bool
     * @throws Exception
     */
    public function delete(string $table, array $condition): bool
    {
        if (!$condition) {
            return false;
        }

        $SQLString = $this->drawSQLString($table, [], [], $condition, [], [], 'delete', false, $this->validateEverything);
        $values = $this->makeValues($condition);
        return $this->execSet($SQLString, $values);
    }

    /**
     * @param string $table
     * @param array $condition
     * @return int
     * @throws Exception
     */
    public function count(string $table, array $condition = []): int
    {
        $SQLString = $this->drawSQLString($table, [], [], $condition, [], [], 'select', true, $this->validateEverything);
        if ($condition) {
            $values = $this->makeValues($condition);
            $result = $this->execGet($SQLString, $values);
        } else {
            $result = $this->execGet($SQLString, []);
        }

        if (!isset($result[0]) || !isset($result[0]['COUNT'])) {
            return 0;
        }

        return (int)$result[0]['COUNT'];
    }

    /**
     * @param string $tableName
     * @return array
     */
    public function dangerouslyGetListOfTables(string $tableName = ''): array
    {
        if (!$tableName) {
            return $this->tables;
        } else {
            return array_filter($this->tables, function ($element) use ($tableName) {
                return $element === $tableName;
            });
        }
    }

    /**
     * @param string $tableName
     * @return bool
     */
    public function doesTableExist(string $tableName): bool
    {
        return in_array($tableName, $this->tables);
    }

    /**
     * @param string $tableName
     * @param bool $assoc
     * @return array
     */
    public function getTableStructure(string $tableName, bool $assoc = false): array
    {
        if (!$this->doesTableExist($tableName)) {
            return [];
        }

        $result = $this->execGet("
          SELECT 
            /* FIELD PROPERTIES */
            FIELDS.TABLE_NAME,
            FIELDS.TABLE_SCHEMA,
            FIELDS.COLUMN_NAME,
            FIELDS.IS_NULLABLE,
            FIELDS.COLUMN_TYPE,
            FIELDS.COLUMN_KEY,
            FIELDS.EXTRA,
            /* ALL ABOUT INDEXES */
            INDEXES.INDEX_NAME,
            INDEXES.INDEX_TYPE,
            INDEXES.NON_UNIQUE,
            /* PRIMARY AND FOREIGN KEYS */
            CONSTRAINTS.CONSTRAINT_NAME,
            CONSTRAINTS.REFERENCED_TABLE_SCHEMA,
            CONSTRAINTS.REFERENCED_TABLE_NAME,
            CONSTRAINTS.REFERENCED_COLUMN_NAME
          FROM INFORMATION_SCHEMA.COLUMNS FIELDS
            LEFT JOIN INFORMATION_SCHEMA.STATISTICS INDEXES 
                ON FIELDS.COLUMN_NAME = INDEXES.COLUMN_NAME 
                AND FIELDS.TABLE_SCHEMA = INDEXES.TABLE_SCHEMA 
                AND FIELDS.TABLE_NAME = INDEXES.TABLE_NAME 
            LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE CONSTRAINTS 
                ON FIELDS.COLUMN_NAME = CONSTRAINTS.COLUMN_NAME 
                AND FIELDS.TABLE_SCHEMA = CONSTRAINTS.TABLE_SCHEMA 
                AND FIELDS.TABLE_NAME = CONSTRAINTS.TABLE_NAME 
          WHERE FIELDS.TABLE_SCHEMA = :dbname AND FIELDS.TABLE_NAME = :tableName ORDER BY FIELDS.ORDINAL_POSITION;
        ", [':dbname' => $this->dbname, ':tableName' => $tableName]);

        if ($assoc) {
            foreach ($result as $key => $structure) {
                $result[$structure['COLUMN_NAME']] = $structure;
                unset ($result[$key]);
            }
        }

        return $result;
    }

    /**
     * @param string $tableName
     * @param bool $assoc
     * @return array
     * @throws Exception
     * @deprecated $this->getTableStructure() returns information about indexes as well
     */
    public function getTableIndexes(string $tableName, bool $assoc = false): array
    {
        if (!$this->doesTableExist($tableName)) {
            return [];
        }
        $result = $this->execGet("SHOW INDEX FROM `$tableName` FROM `$this->dbname`;");
        if ($assoc) {
            foreach ($result as $key => $index) {
                $result[$index['Column_name']] = $index;
                unset ($result[$key]);
            }
        }
        return $result;
    }

    /**
     * @param string $tableName
     * @param bool $assoc
     * @return array
     * @deprecated $this->getTableStructure() returns information about foreign keys as well
     */
    public function getTableForeignKeys(string $tableName, bool $assoc = false): array
    {
        if (!$this->doesTableExist($tableName)) {
            return [];
        }
        $result = $this->execGet("
            SELECT 
                TABLE_NAME, TABLE_SCHEMA, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
            FROM 
                INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE 
                TABLE_SCHEMA = :dbname AND
                TABLE_NAME   = :tableName AND
                REFERENCED_TABLE_NAME IS NOT NULL;
        ", [':dbname' => $this->dbname, ':tableName' => $tableName]);
        if ($assoc) {
            foreach ($result as $key => $value) {
                $result[$value['COLUMN_NAME']] = $value;
                unset ($result[$key]);
            }
        }
        return $result;
    }

    /**
     * @param string $table
     * @param array $join
     * @param array $updateFields
     * @param array $condition
     * @param array $orderBy
     * @param array $limit
     * @param string $type
     * @param bool $count
     * @param bool $validateEverything
     * @return string
     * @throws Exception
     */
    private function drawSQLString(string $table, array $join = [], array $updateFields = [], array $condition = [], array $orderBy = [], array $limit = [], string $type = 'select', bool $count = false, bool $validateEverything = true): string
    {
        $table = $this->prepareTable($table);

        // unfortunately it's just impossible to turn off without destroying everything
        if ($validateEverything && true) {
            $join = $this->prepareJoin($join, $table);
            $condition = $this->prepareCondition($condition, $table);
            $updateFields = $this->prepareCondition($updateFields, $table);
            $orderBy = $this->prepareOrderBy($orderBy, $table);
            $limit = $this->prepareLimit($limit);
        }

        $SQLString = '';
        switch ($type) {
            case 'select':
                $SQLString = $this->drawSelect($table, $join, $condition, $orderBy, $limit, $count);
                break;
            case 'insert':
                $SQLString = $this->drawInsert($table, $condition);
                break;
            case 'update':
                $SQLString = $this->drawUpdate($table, $updateFields, $condition);
                break;
            case 'delete':
                $SQLString = $this->drawDelete($table, $condition);
                break;
            default:
                $this->throwException('DRAW: Incorrect type');
        }

        return $SQLString;
    }

    /**
     * @param string $table
     * @param array $condition
     * @return string
     */
    private function drawDelete(string $table, array $condition): string
    {
        $SQLWhere = $this->drawWhere($condition, $table);
        return "DELETE FROM " . $table . " WHERE " . $SQLWhere;
    }

    /**
     * @param string $table
     * @param array $fields
     * @param array $condition
     * @return string
     */
    private function drawUpdate(string $table, array $fields, array $condition): string
    {
        $SQLWhat = $this->drawValues($fields, 'update');
        $SQLWhere = $this->drawWhere($condition, $table);
        return "UPDATE " . $table . " SET " . $SQLWhat . " WHERE " . $SQLWhere;
    }

    /**
     * @param string $table
     * @param array $condition
     * @return string
     */
    private function drawInsert(string $table, array $condition): string
    {
        $data = $this->drawValues($condition, 'insert');
        return "INSERT INTO " . $table . " ( " . $data['fields'] . " ) VALUES ( " . $data['values'] . " )";
    }

    /**
     * @param string $table
     * @param array $condition
     * @param array $orderBy
     * @param array $limit
     * @param bool $count
     * @param array $join
     * @return string
     * @throws Exception
     */
    private function drawSelect(string $table, array $join, array $condition, array $orderBy, array $limit, bool $count = false): string
    {
        // we assume that everything is prepared
        // all that function used to validate data, but now it is unnecessary
        $SQLWhat = $this->drawWhat($table, $join);
        $SQLOrderBy = $this->drawOrderBy($orderBy, $table);
        $SQLLimit = $this->drawLimit($limit);
        $SQLJoin = $this->drawJoin($join, $table);
        $SQLWhere = $this->drawWhere($condition, $table);

        return "SELECT " . ($count ? "COUNT(*) AS COUNT" : $SQLWhat) . " FROM " . $table . $SQLJoin . ($condition ? " WHERE " . $SQLWhere : "") . $SQLOrderBy . $SQLLimit;
    }

    /**
     * @param array $condition
     * @param string $type
     * @return array|string
     */
    private function drawValues(array $condition, string $type = 'insert')
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
                $sql .= $eq . $key . " = :set_" . $key;
                $eq = ", ";
            }

            return $sql;
        }

        return '';
    }

    /**
     * @param string $table
     * @param array $join
     * @return string
     * @throws Exception
     */
    private function drawWhat(string $table, array $join): string
    {
        if (!isset($this->fields[$table])) {
            $this->setFields($table);
        }

        $fields = $this->fields[$table];
        $result = "";
        $delimiter = ", ";

        // main table
        foreach ($fields as $field => $value) {
            $result .= $table . '.' . $field . $delimiter;
        }
        // join tables
        foreach ($join as $condition) {
            $joinTable = $condition[1];
            if (!isset($this->fields[$joinTable])) {
                $this->setFields($joinTable);
            }
            $joinFields = $this->fields[$joinTable];
            foreach ($joinFields as $field => $value) {
                if (array_key_exists($field, $fields)) {
                    $field = $field . ' AS ' . $joinTable . '_' . $field;
                }
                $result .= $joinTable . '.' . $field . $delimiter;
            }
            $fields = array_merge($fields, $joinFields);
        }

        return rtrim($result, $delimiter);
    }

    /**
     * @param array $orderBy
     * @param string $table
     * @return string
     */
    private function drawOrderBy(array $orderBy, string $table): string
    {
        $result = "";
        $delimiter = ", ";
        foreach ($orderBy as $field => $key) {
            $result .= $table . '.' . $field . " " . strtoupper($key) . $delimiter;
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

        return " LIMIT " . $limit[0] . (!empty($limit[1]) ? ", " . $limit[1] : "");
    }

    /**
     * @param array $join
     * @param string $table
     * @return string
     * @throws Exception
     */
    private function drawJoin(array $join, string $table): string
    {
        $result = "";
        $delimiter = " ";
        foreach ($join as $key => $condition) {
            $joinType = strtoupper($condition[0]);
            $joinTable = $condition[1];
            $joinCondition = $this->drawOn($condition[2], $joinTable, $table);
            $result .= $joinType . " JOIN " . $joinTable . " ON " . $joinCondition . $delimiter;
        }

        $result = rtrim($result, " ");
        if ($result) {
            $result = " " . $result;
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
        $result = '';
        $delimiter = '';
        foreach ($conditions as $joinField => $mainField) {
            $result .= $delimiter . $joinTable . '.' . $joinField . ' = ' . $mainTable . '.' . $mainField;
            $delimiter = ' AND ';
        }

        return $result;
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
                    $placeholders .= ',:' . $field . $key;
                }
                $placeholders = substr($placeholders, 1);
                $result .= $table . '.' . $field . ' IN (' . $placeholders . ')';
            } else {
                // regular where
                if ($result) {
                    $clause = strpos($field, "!") === 0 ? " OR " : " AND ";
                }
                $operator = strpos($value, "!") === 0 ? "<>" : "=";
                $result .= $clause . $table . '.' . $field . " " . $operator . " :" . $field;
            }
        }

        return $result;
    }

    /**
     * @param array $orderBy
     * @param string $table
     * @param bool $dieIfIncorrect
     * @return array
     * @throws CoreException
     */
    private function prepareOrderBy(array $orderBy = [], string $table = '', bool $dieIfIncorrect = false): array
    {
        if (!$orderBy) {
            return [];
        }

        if (!isset($this->fields[$table])) {
            $this->setFields($table);
        }

        foreach ($orderBy as $field => $type) {
            if (!array_key_exists($field, $this->fields[$table])) {
                $this->throwException("ORDER BY: Given field does not exist in the table", $dieIfIncorrect);
            }

            if (!in_array($type, $this->orderTypes)) {
                $this->throwException("ORDER BY: Incorrect type", $dieIfIncorrect);
            }
        }

        return $orderBy;
    }

    /**
     * @param array $condition
     * @param string $table
     * @param bool $dieIfIncorrect
     * @return array
     * @throws CoreException
     */
    private function prepareCondition(array $condition = [], string $table = '', bool $dieIfIncorrect = false): array
    {
        if (!$condition) {
            return [];
        }

        if (!isset($this->fields[$table])) {
            $this->setFields($table);
        }

        foreach ($condition as $field => $value) {
            // do not check values, at this point we don't need it
            if (!array_key_exists($field, $this->fields[$table])) {
                $this->throwException("CONDITION: Given field does not exist in the table", $dieIfIncorrect);
            }
        }

        return $condition;
    }

    /**
     * @param array $join
     * @param string $table
     * @param bool $dieIfIncorrect
     * @return array
     * @throws Exception
     */
    private function prepareJoin(array $join = [], string $table = '', bool $dieIfIncorrect = false): array
    {
        if (!$join) {
            return [];
        }
        /**
         * join => [
         *      ['TYPE', 'table', ['JoinField' => 'MainField']]
         * ]
         */
        foreach ($join as $key => $joinItem) {
            $joinType = $joinItem[0];
            if (!in_array($joinType, $this->joinTypes)) {
                $this->throwException("JOIN: Incorrect type, allowed only " . implode('/', $this->joinTypes), $dieIfIncorrect);
            }
//            $join[$key][0] = $joinType;

            $joinTable = $joinItem[1];
            // it also add _view prefix
            $join[$key][1] = $this->prepareTable($joinTable);


            $joinCondition = $joinItem[2];
            if (!is_array($joinCondition)) {
                $this->throwException("JOIN: Expected condition to be an array, but given " . gettype($joinCondition), $dieIfIncorrect);
            }

            foreach ($joinCondition as $joinField => $mainField) {
                if (!isset($this->fields[$table])) {
                    $this->setFields($table);
                }
                if (!isset($this->fields[$joinTable])) {
                    $this->setFields($joinTable);
                }

                if (!array_key_exists($mainField, $this->fields[$table]) || !array_key_exists($joinField, $this->fields[$joinTable])) {
                    $this->throwException("JOIN: Given field does not exist in the table", $dieIfIncorrect);
                }
//                $join[$key][2][$joinField] = $mainField;
            }
        }

        return $join;
    }

    /**
     * @param string $table
     * @param bool $dieIfIncorrect
     * @return string
     * @throws Exception
     */
    public function prepareTable(string $table, bool $dieIfIncorrect = false): string
    {
        if (!$this->doesTableExist($table)) {
            throw new CoreException("TABLE: The specified table does not exist");
        }

        // check view
        // todo remove all this stuff and move it to migrations
        $viewName = $table . $this->postfix;
        if (!in_array($viewName, $this->views)) {
            // if view does not exist -> let's try to create it
            $sql = "CREATE VIEW $viewName AS SELECT * FROM $table WHERE $this->partitionColumnName = $this->partitionFunction()";
            $result = $this->dangerouslySendQueryWithoutPreparation($sql);
            if (!$result) {
                $this->throwException("TABLE: View does not exist and it's impossible to create one", $dieIfIncorrect);
            }

            // everything is fine
            $this->views[] = $viewName;
        }

        // check triggers
        $triggerName = $table . $this->triggerPostfix;
        if (!in_array($triggerName, $this->triggers)) {
            $sql = "CREATE TRIGGER $triggerName
                      BEFORE INSERT ON $table 
                      FOR EACH ROW
                      SET new._config_id = $this->partitionFunction()";
            $result = $this->dangerouslySendQueryWithoutPreparation($sql);
            if (!$result) {
                $this->throwException("TABLE: Trigger does not exist and it's impossible to create one", $dieIfIncorrect);
            }
            $this->triggers[] = $triggerName;
        }

        // If everything is fine
        return $viewName;
    }

    /**
     * @throws CoreException
     */
    public function dump(): string
    {
        if (!file_exists($this->dumpFileLocation)) {
            mkdir($this->dumpFileLocation, 0666, true);
        }
        $database = Config::$db['database'];
        $password = Config::$db['password'];
        $host = Config::$db['host'];
        $user = Config::$db['username'];
        $fileName = realpath($this->dumpFileLocation) . "/{$database}_backup_" . time() . '.sql';
        // i don't know, this code looks unsafe to me
        @exec("mysqldump --user=$user --password=$password --host=$host $database > \"$fileName\"");
        if (!file_exists($fileName)) {
            $this->throwException('Unable to dump database. See docs here: https://phpjet.org/docs/database');
        }
        // if everything is fine
        return realpath($fileName);
    }

    /**
     * @param array $limit
     * @return array
     * @throws Exception
     */
    private function prepareLimit(array $limit): array
    {
        if (!$limit) {
            return [];
        }

        if (!isset($limit[0])) {
            $this->throwException("LIMIT: Array should contain one or two integer values");
        }
        $limit[0] = (int)$limit[0];

        if (isset($limit[1])) {
            $limit[1] = (int)$limit[1];
        }

        return $limit;
    }

    /**
     * @param array $condition
     * @param string|null $prefix
     * @return array
     */
    private function makeValues(array $condition, string $prefix = null): array
    {
        if (!$condition) {
            return [];
        }

        if ($prefix) {
            $prefix = $prefix . "_";
        }

        $values = [];
        foreach ($condition as $field => $item) {
            if (is_array($item)) {
                foreach ($item as $key => $_item) {
                    $values[":" . $prefix . $field . $key] = $_item;
                }
            } else {
                if (strpos($item, "!") === 0) {
                    $item = substr($item, 1);
                }
                $values[":" . $prefix . $field] = $item;
            }
        }

        return $values;
    }

    /**
     * @param string $table
     * @param array $params
     * @return array
     * @throws Exception
     */
    private function setEmptyFields(string $table, array $params): array
    {
        $table = $this->prepareTable($table);
        if (!in_array($table, $this->fields)) {
            $this->setFields($table);
        }

        foreach ($params as $key => $value) {
            if ($value === $this->null) {
                $params[$key] = null;
                continue;
            }
            if (empty($value)) {
                $key_clean = substr($key, 1);
                if (strpos($key_clean, "set_") !== false) {
                    $key_clean = substr($key_clean, 4);
                }

                $params[$key] = array_key_exists($key_clean, $this->fields[$table]) ? $this->fields[$table][$key_clean] : null;
            }
        }

        return $params;
    }

    /**
     * @param string $table
     * @return bool
     * @throws Exception
     */
    private function setFields(string $table): bool
    {
        if (isset($this->fields[$table])) {
            return true;
        }

        $fields = $this->execGet("SHOW FIELDS FROM " . $table);
        foreach ($fields as $key => $value) {
            $type = strtolower($value['Type']);
            if (($end = strpos($type, '(')) !== false) {
                $type = substr($type, 0, $end);
            }
            $empty = array_key_exists($type, $this->types) ? $this->types[$type] : null;
            $this->fields[$table][$value['Field']] = $empty;
        }

        return true;
    }

    /**
     * @param string $rawString
     * @return string
     */
    private function returnValidIdentifier(string $rawString): string
    {
        $string = trim($rawString);
        $string = preg_replace('/[^A-Za-z0-9_]/', '', $string);
        $string = strtolower($string);
        return $string;
    }

    /**
     * @param bool $return
     * @return void
     */
    private function showTables(bool $return = false): void
    {
        $rows = $this->execGet("show full tables");
        $rowKey = 'Tables_in_' . Config::$db['database'];

        foreach ($rows as $key => $value) {
            if ($value['Table_type'] === 'BASE TABLE') {
                $this->tables[] = $value[$rowKey];
            } else if ($value['Table_type'] === 'VIEW') {
                $this->views[] = $value[$rowKey];
            }
        }
    }

    private function showTriggers(): void
    {
        $rows = $this->execGet("show triggers");

        foreach ($rows as $key => $value) {
            $this->triggers[] = $value['Trigger'];
        }
    }

    /**
     * @param array $result
     * @return array|string
     */
    private function removeSpecialChars(array $result): array
    {
        return PHPJet::$app->tool->utils->removeSpecialChars($result);
    }

    /**
     * @param string $message
     * @param bool $die
     * @throws CoreException
     * @deprecated
     */
    private function throwException(string $message, bool $die = false)
    {
        if (!Config::$dev['debug']) {
            $message = $this->defaultErrorMessage;
        }
        if ($die) {
            PHPJet::$app->exit($message);
        } else {
            throw new CoreException($message);
        }
    }
}