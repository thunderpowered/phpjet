<?php


namespace Jet\App\Engine\ActiveRecord\Utils;

use Exception;
use Jet\App\Engine\ActiveRecord\_FieldIndex;
use Jet\App\Engine\ActiveRecord\_FieldType;
use Jet\App\Engine\ActiveRecord\Table;
use Jet\App\Engine\Core\Store;

/**
 * Class Checker
 * @package Jet\App\Engine\ActiveRecord
 */
class Checker
{
    /**
     * @var Table
     */
    private $table;
    /**
     * @var Store
     */
    private $store;
    /**
     * Checker constructor.
     */
    public function __construct(Table $table, Store $store)
    {
        $this->table = $table;
        $this->store = $store;
    }

    /**
     * @return _TableStatus
     * @throws Exception
     */
    public function returnTableStatus(): _TableStatus
    {
        $status = new _TableStatus();
        /*
         * Same classes may not participate in the process, they're marked with the '_ignored' flag
         */
        if ($this->table->_ignore) {
            $status->ignored = true;
            return $status;
        }
        /**
         * Quick check if table exists
         */
        $tableName = $this->table->_returnDatabaseName();
        if (!$this->store->doesTableExist($tableName)) {
            $status->doesNotExist = true;
            return $status;
        }
        /**
         * Actual check
         */
        $fields = $this->table->_returnAllFields();
        // contains information about fields and data types
        $structure = $this->store->getTableStructure($tableName, true);
        // contains information about indexes including primary keys
        $indexes = $this->store->getTableIndexes($tableName, true);
        // contains information about foreign keys (orly?)
        $foreignKeys = $this->store->getTableForeignKeys($tableName, true);

        foreach ($fields as $field) {
            /**
             * step 1. check field type
             */
            $fieldType = $this->table->_getFieldType($field);
            $status->type = $this->checkFieldType($fieldType, $structure[$field]);
            /**
             * step 2. check indexes
             */
            $index = $this->table->_getFieldIndex($field);
            $status->index = $this->checkIndex($index, $indexes[$field]);
            /**
             * step 3. check foreign keys
             */
            $status->foreignKey = $this->checkForeignKeys($index, $foreignKeys[$field]);
            /**
             * step 4. todo add more deep checks and combine indexes/foreign keys into single constraint type
             */
        }
        return $status;
    }

    /**
     * @param _FieldType $fieldType
     * @param array $dbFieldType
     * @return array
     */
    private function checkFieldType(_FieldType $fieldType, array $dbFieldType): array
    {
        return [];
    }

    /**
     * @param _FieldIndex $index
     * @param array $dbIndex
     * @return array
     */
    private function checkIndex(_FieldIndex $index, array $dbIndex): array
    {
        return [];
    }

    /**
     *
     */
    private function checkForeignKeys(_FieldIndex $index, array $dbForeignKey): array
    {
        return [];
    }
}