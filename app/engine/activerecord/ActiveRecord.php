<?php
/**
 * ActiveRecord is a pattern that connect database and classes in programming code.
 * Every child class points to particular table in database.
 * Every object of class is a row in the table.
 * Thanks @mixtech911 for the idea.
 */

namespace CloudStore\App\Engine\ActiveRecord;

use CloudStore\CloudStore;

/**
 * Class ActiveRecord
 * @package CloudStore\App\Engine\Core
 */
abstract class ActiveRecord
{
    /**
     * @var string
     */
    protected $_class;
    /**
     * @var bool
     */
    protected $_loaded;

    /**
     * @var string
     */
    protected $_primaryKey;

    /**
     * ActiveRecord constructor.
     * @param bool $loaded
     */
    public function __construct(bool $loaded = false)
    {
        // if primary key does not exist
        if (!$this->_primaryKey) {
            CloudStore::$app->exit('Primary Key Not Set');
        }
        // _class contains the name of the class
        $this->_class = get_class($this);

        // _loaded means that row was loaded from database, not created by user
        $this->_loaded = $loaded;
    }

    /**
     * @param array $conditions
     * @param array $orderBy
     * @param array $limit
     * @param bool $removeSpecialChars
     * @return ActiveRecord|void
     */
    public static function getOne(array $conditions = array(), array $orderBy = array(), array $limit = array(), bool $removeSpecialChars = true)
    {
        /**
         * Call hooks
         */
        self::beforeSelect();

        /**
         * Since it is static method, we should not use $class in this context.
         * Instead, we create new name of class
         */
        $class = get_called_class();

        $table = self::convertClassNameIntoTableName($class);
        $rows = CloudStore::$app->store->load($table, $conditions, $orderBy, $limit, $removeSpecialChars);
        if (isset($rows[0])) {
            return self::convertRowIntoObject($rows[0], $class, true);
        }

        // just return, if nothing found - nothing to return. perfection
        return;
    }

    /**
     * @param array $conditions
     * @param array $orderBy
     * @param array $limit
     * @return static[]
     */
    public static function get(array $conditions = array(), array $orderBy = array(), array $limit = array()): array
    {
        /**
         * Do the same for for many rows
         */
        self::beforeSelect();
        $class = get_called_class();
        $table = self::convertClassNameIntoTableName($class);
        $rows = CloudStore::$app->store->load($table, $conditions, $orderBy, $limit);

        /**
         * Create an array of objects
         */
        $result = [];
        foreach ($rows as $key => $row) {
            $result[] = self::convertRowIntoObject($row, $class, true);
        }

        return $result;
    }

    /**
     * @param array $join
     * @param array $conditions
     * @param array $orderBy
     * @param array $limit
     * @return static[]
     */
    public static function getJoin(array $join = array(), array $conditions = array(), array $orderBy = array(), array $limit = array()): array
    {
        self::beforeSelect();
        $class = get_called_class();
        $table = self::convertClassNameIntoTableName($class);
        $rows = CloudStore::$app->store->loadNew($table, $join, $conditions, $orderBy, $limit);

        // Create an array of objects
        $result = [];
        foreach ($rows as $key => $row) {
            $result[] = self::convertRowIntoObject($row, $class, true);
        }

        return $result;
    }

    /**
     * @param array $conditions
     * @return int
     */
    public static function count(array $conditions = array()): int
    {
        $class = get_called_class();
        $table = self::convertClassNameIntoTableName($class);
        return CloudStore::$app->store->count($table, $conditions);
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        /**
         * Convert object property into assoc array
         */
        $row = $this->convertObjectIntoRow();
        $table = $this->convertClassNameIntoTableName($this->_class);

        /**
         * If row was loaded from database, update it.
         * If not - create new
         */
        $result = false;
        if ($this->_loaded) {
            /**
             * Hooks
             */
            $this->beforeUpdate();
            /**
             * Yeah, i haven't find better solution for this :)
             * So i guess it's temporary
             */
            CloudStore::$app->store->update($table, $row, [$this->_primaryKey => $this->$this->primaryKey]);
        } else {
            /**
             * Hooks
             */
            $this->beforeInsert();
            $result = CloudStore::$app->store->collect($table, $row);
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function remove(): bool
    {
        /**
         * Hooks
         */
        $this->beforeDelete();

        $table = $this->convertClassNameIntoTableName($this->_class);

        return CloudStore::$app->store->delete($table, [$this->_primaryKey => $this->$this->primaryKey]);
    }

    /**
     * Hooks is a class of functions that can be called in particular moments.
     * So you can define your own function in the child class and do something you want.
     * Because of get method that is actually static methods, hook also should be static.
     */
    protected static function beforeSelect()
    {
    }

    /**
     * Non-static hooks
     */
    protected function beforeInsert()
    {
    }

    protected function beforeDelete()
    {
    }

    protected function beforeUpdate()
    {
    }

    /**
     * @param array $row
     * @param string $class
     * @param bool $loaded
     * @return static
     */
    private static function convertRowIntoObject(array $row, string $class, bool $loaded = true): ActiveRecord
    {
        /**
         * Create object from a row and return it
         */
        $object = new $class($loaded);
        foreach ($row as $key => $value) {
            $object->$key = $value;
        }

        return $object;
    }

    /**
     * @param string $className
     * @return string
     */
    private static function convertClassNameIntoTableName(string $className): string
    {
        $className = explode("\\", $className);
        $className = $className[count($className) - 1];
        return strtolower($className);
    }

    /**
     * @return array
     */
    private function convertObjectIntoRow(): array
    {
        $row = [];
        foreach ($this as $prop => $value) {
            if ($this->isSystemProperty($prop)) {
                continue;
            }
            $row[$prop] = $value;
        }

        return $row;
    }

    /**
     * @param string $propName
     * @return bool
     */
    private function isSystemProperty(string $propName): bool
    {
        return (strpos($propName, "_") === 0);
    }

    /**
     * @param array $objects
     * @return object
     */
    private function mergeObjects(array $objects): object
    {

    }
}