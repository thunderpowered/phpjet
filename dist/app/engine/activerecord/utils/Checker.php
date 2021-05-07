<?php


namespace Jet\App\Engine\ActiveRecord\Utils;

use Exception;
use Jet\App\Engine\ActiveRecord\_FieldAttributes;
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
        $tableStatus = new _TableStatus();

        if ($this->table->_ignore) {
            $tableStatus->ignored = true;
            return $tableStatus;
        }

        $tableName = $this->table->_returnDatabaseName();
        if ($this->store->doesTableExist($tableName)) {
            $tableStatus->exists = true;
        } else {
            $tableStatus->exists = false;
            // no need to check anything else at this point
            return $tableStatus;
        }

        $fields = $this->table->_returnAllFields();
        $structure = $this->store->getTableStructure($tableName, true);

        foreach ($fields as $field) {
            $fieldStatus = new _FieldStatus();

            if (isset($structure[$field]) && is_array($structure[$field])) {
                $fieldStatus->exists = true;

                $fieldType = $this->table->_getFieldType($field);
                $fieldAttributes = $this->table->_getFieldAttributes($field);
                $fieldStatus = $this->checkFieldType($fieldType, $fieldAttributes, $structure[$field], $fieldStatus);

                $index = $this->table->_getFieldIndex($field);
                $fieldStatus = $this->checkIndex($index, $structure[$field], $fieldStatus);

                $fieldStatus = $this->checkForeignKeys($index, $structure[$field], $fieldStatus);
            } else {
                $tableStatus->exists = false;
            }

            $tableStatus->fields[$field] = $fieldStatus;
        }
        return $tableStatus;
    }

    /**
     * @param _FieldType $fieldType
     * @param _FieldAttributes $fieldAttributes
     * @param array $dbFieldType
     * @param _FieldStatus $fieldStatus
     * @return _FieldStatus
     */
    private function checkFieldType(_FieldType $fieldType, _FieldAttributes $fieldAttributes, array $dbFieldType, _FieldStatus $fieldStatus): _FieldStatus
    {
        // quick note: true/false means match/mismatch of parameters
        // if something mismatches -> just rewrite db params with active-record params
        $fieldStatus->type = $fieldType->type === $dbFieldType['COLUMN_TYPE'];
        $fieldStatus->NULL = $fieldAttributes->null === !($dbFieldType['IS_NULLABLE'] === 'NO');
        return $fieldStatus;
    }

    /**
     * @param _FieldIndex $index
     * @param array $dbIndex
     * @param _FieldStatus $fieldStatus
     * @return _FieldStatus
     */
    private function checkIndex(_FieldIndex $index, array $dbIndex, _FieldStatus $fieldStatus): _FieldStatus
    {
        // todo unite 'index' and 'primary' properties
        $fieldStatus->index = !!$index->index === !!$dbIndex['INDEX_NAME'];
        $fieldStatus->indexKey = $index->key === $dbIndex['COLUMN_KEY'];
        $fieldStatus->indexType = $index->type === $dbIndex['INDEX_TYPE'];
        // this may be not obvious, since in PHPJet we use 'unique' field, but in MySQL schema there's 'non-unique'
        // so if these params match, they actually mismatch
        // more obvious line - !(!$index->unique !== (bool)$dbIndex['Non_unique'])
        $fieldStatus->indexUnique = $index->unique === (bool)$dbIndex['NON_UNIQUE'];
        return $fieldStatus;
    }

    /**
     * @param _FieldIndex $index
     * @param array $dbForeignKey
     * @param _FieldStatus $fieldStatus
     * @return _FieldStatus
     */
    private function checkForeignKeys(_FieldIndex $index, array $dbForeignKey, _FieldStatus $fieldStatus): _FieldStatus
    {
        $fieldStatus->foreignKey = !!$index->foreignKey === ($dbForeignKey['REFERENCED_COLUMN_NAME'] && $dbForeignKey['REFERENCED_TABLE_NAME'] && $dbForeignKey['REFERENCED_TABLE_SCHEMA']);
        $fieldStatus->foreignKeyTable = $index->foreignKeyField->table === $dbForeignKey['REFERENCED_TABLE_NAME'];
        $fieldStatus->foreignKeyField = $index->foreignKeyField->field === $dbForeignKey['REFERENCED_COLUMN_NAME'];
        return $fieldStatus;
    }
}