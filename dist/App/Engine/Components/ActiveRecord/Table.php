<?php
/**
 * Table is a pattern that connect database and classes in programming code.
 * Every child class points to particular table in database.
 * Every object of class is a row in the table.
 * Thanks @mixtech911 for the idea.
 */

namespace Jet\App\Engine\Components\ActiveRecord;

use Exception;
use Jet\App\Engine\Components\ActiveRecord\Tables\_Config;
use Jet\App\Engine\Components\ActiveRecord\utils\_TableStatus;
use Jet\App\Engine\Components\ActiveRecord\Utils\Builder;
use Jet\App\Engine\Components\ActiveRecord\Utils\Checker;
use Jet\App\Engine\Exceptions\CoreException;
use Jet\PHPJet;
use stdClass;

/**
 * Class Table
 * @package Jet\App\Engine\Core
 */
abstract class Table
{
    /**
     * @var int
     * using only for migrations
     */
    public $_status;
    /**
     * @var bool
     */
    public $_ignore;
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
    protected $_primaryKey = 'id';
    /**
     * @var Field
     */
    protected $_config_id;
    /**
     * @var Field
     */
    protected $_created;
    /**
     * @var Field
     */
    protected $_updated;
    /**
     * @var Field
     */
    protected $_deleted;
    /**
     * @var bool
     */
    protected $_has_data = false;

    /**
     * Table constructor.
     * @param bool $loaded
     */
    public function __construct(bool $loaded = false)
    {
        // if primary key does not exist
        if (!$this->_primaryKey) {
            PHPJet::$app->exit('Primary Key Not Set');
        }
        // _class contains the name of the class
        $this->_class = get_class($this);
        // _loaded means that row was loaded from database, not created by user
        $this->_loaded = $loaded;

        // system fields
        if ($this->_returnDatabaseName() !== '_config') {
            // because _config_id field points to _config table
            $this->_config_id = Field::int()->setForeignKey(
                (new _Config())->_getFieldType('id')
            );
        }
        $this->_created = Field::dateTime();
        $this->_updated = Field::dateTime();
        $this->_deleted = Field::dateTime();

        // temporary solution, local exception handler
        set_exception_handler([$this, 'exceptionHandler']);
    }

    /**
     * @param array $conditions
     * @param array $orderBy
     * @param array $limit
     * @param bool $removeSpecialChars
     * @return static|void
     */
    public static function getOne(array $conditions = array(), array $orderBy = array(), array $limit = array(0, 1), bool $removeSpecialChars = true)
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

        try {
            $rows = PHPJet::$app->store->load($table, $conditions, $orderBy, $limit, $removeSpecialChars);
        } catch (Exception $e) {
            self::exceptionHandler($e);
        }

        if (isset($rows[0])) {
            return self::convertRowIntoObject($rows[0], $class, true);
        }

        return;
    }

    /**
     * @param array $conditions
     * @param array $orderBy
     * @param array $limit
     * @param bool $removeSpecialChars
     * @return static[]
     */
    public static function get(array $conditions = array(), array $orderBy = array(), array $limit = array(), bool $removeSpecialChars = true): array
    {
        /**
         * Do the same for for many rows
         */
        self::beforeSelect();
        $class = get_called_class();
        $table = self::convertClassNameIntoTableName($class);
        try {
            $rows = PHPJet::$app->store->load($table, $conditions, $orderBy, $limit, $removeSpecialChars);
        } catch (Exception $e) {
            self::exceptionHandler($e);
        }

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
     * @return array
     */
    public static function getJoin(array $join = array(), array $conditions = array(), array $orderBy = array(), array $limit = array()): array
    {
        self::beforeSelect();
        $class = get_called_class();
        $table = self::convertClassNameIntoTableName($class);

        try {
            $rows = PHPJet::$app->store->load2($table, $join, $conditions, $orderBy, $limit);
        } catch (Exception $e) {
            self::exceptionHandler($e);
        }

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
     * @throws Exception
     */
    public static function count(array $conditions = array()): int
    {
        $class = get_called_class();
        $table = self::convertClassNameIntoTableName($class);
        try {
            $result = PHPJet::$app->store->count($table, $conditions);
        } catch (Exception $e) {
            self::exceptionHandler($e);
        }

        return $result;
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
        if ($this->_loaded) {
            /**
             * Hooks
             */
            $this->beforeUpdate();
            /**
             * Yeah, i haven't find better solution for this :)
             * So i guess it's temporary
             */
            try {
                $result = PHPJet::$app->store->update($table, $row, [$this->_primaryKey => $this->{$this->_primaryKey}]);
            } catch (Exception $e) {
                self::exceptionHandler($e);
            }
        } else {
            /**
             * Hooks
             */
            $this->beforeInsert();
            try {
                $result = PHPJet::$app->store->collect($table, $row);
            } catch (Exception $e) {
                self::exceptionHandler($e);
            }
        }

        return $result;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function remove(): bool
    {
        /**
         * Hooks
         */
        $this->beforeDelete();

        $table = $this->convertClassNameIntoTableName($this->_class);

        return PHPJet::$app->store->delete($table, [$this->_primaryKey => $this->$this->primaryKey]);
    }

    /**
     * @return int
     */
    public function lastInsertId(): int
    {
        return (int)PHPJet::$app->store->getLastInsertId();
    }

    /**
     * 0 - table ignored
     * 1 - table does not exist
     * 2 - table exists, yet outdated
     * 3 - table exists and up to date
     * @return _TableStatus
     * @throws CoreException
     */
    public function returnStatus(): _TableStatus
    {
        $checker = new Checker($this, PHPJet::$app->store);
        return $checker->returnTableStatus();
    }

    /**
     * @param Exception $e
     */
    protected static function exceptionHandler(Exception $e)
    {
        PHPJet::$app->error->exceptionCatcher($e);
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
    private static function convertRowIntoObject(array $row, string $class, bool $loaded = true): Table
    {
        /**
         * Create object from a row and return it
         */
        $object = new $class($loaded);
        foreach ($row as $key => $value) {
            if ($object->$key instanceof Field) {
                $object->$key->_setValue($value);
            } else {
                trigger_error("Field $key should be instance of Field", E_USER_NOTICE);
            }
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
     * @param string $fieldName
     * @return null|mixed
     */
    public function __get(string $fieldName)
    {
        // todo remove double checking of object class
        if (!isset($this->$fieldName) || $this->isSystemProperty($fieldName) || !($this->$fieldName instanceof Field)) {
            return null;
        }
        if ($this->$fieldName->_hasValue()) {
            return $this->$fieldName->_getValue();
        } else {
            return $this->_getFieldType($fieldName);
        }
    }

    /**
     * @param string $fieldName
     * @param $value
     */
    public function __set(string $fieldName, $value)
    {
        if (!isset($this->$fieldName) || !($this->$fieldName instanceof Field)) {
            trigger_error("Property $fieldName does not exist in '" . get_class($this) . "'", E_USER_NOTICE);
        } else {
            $this->$fieldName->_setValue($value);
        }
    }

    /**
     * @return string
     * @deprecated returns name in db-like style (lowercase, without namespaces etc.)
     */
    public function _returnDatabaseName(): string
    {
        return self::convertClassNameIntoTableName(get_class($this));
    }

    /**
     * @return array
     */
    public function _returnAllFields(): array
    {
        $fields = [];
        foreach ($this as $fieldName => $fieldValue) {
            if (!($this->$fieldName instanceof Field)) {
                continue;
            }
            $fields[$fieldName] = $this->_getFieldType($fieldName);
        }
        return $fields;
    }

    /**
     * @param string $fieldName
     * @return _FieldType
     */
    public function _getFieldType(string $fieldName): _FieldType
    {
        $table = $this->_returnDatabaseName();
        $type = $this->$fieldName->_getType();
        $type->table = $table;
        $type->field = $fieldName;
        return $type;
    }

    /**
     * @param string $fieldName
     * @return _FieldAttributes
     */
    public function _getFieldAttributes(string $fieldName): _FieldAttributes
    {
        return $this->$fieldName->_getAttributes();
    }

    /**
     * @param string $fieldName
     * @return _FieldIndex
     */
    public function _getFieldIndex(string $fieldName): _FieldIndex
    {
        return $this->$fieldName->_getIndex();
    }
}