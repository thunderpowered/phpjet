<?php


namespace Jet\App\Engine\Components\ActiveRecord\Utils;

use Exception;
use Jet\App\Engine\Components\ActiveRecord\_FieldAttributes;
use Jet\App\Engine\Components\ActiveRecord\_FieldIndex;
use Jet\App\Engine\Components\ActiveRecord\_FieldType;
use Jet\App\Engine\Components\ActiveRecord\Table;
use Jet\App\Engine\Core\Store;
use Jet\App\Engine\Exceptions\CoreException;

/**
 * Class Checker
 * @package Jet\App\Engine\Components\ActiveRecord
 */
class Checker
{
    public const TABLE_STATUS_IGNORED = 0;
    public const TABLE_STATUS_DOES_NOT_EXIST = 1;
    public const TABLE_STATUS_OUTDATED = 2;
    public const TABLE_STATUS_UP_TO_DATE = 3;
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
     * @throws CoreException
     */
    public function returnTableStatus(): _TableStatus
    {
        $tableStatus = new _TableStatus();

        if ($this->table->_ignore) {
            $tableStatus->ignored = true;
            $tableStatus->status = self::TABLE_STATUS_IGNORED;
            return $tableStatus;
        }

        $tableName = $this->table->_returnDatabaseName();
        if ($this->store->doesTableExist($tableName)) {
            $tableStatus->exists = true;
        } else {
            $tableStatus->exists = false;
            $tableStatus->status = self::TABLE_STATUS_DOES_NOT_EXIST;
            // no need to check anything else at this point
            return $tableStatus;
        }

        $fields = $this->table->_returnAllFields();
        $structure = $this->store->getTableStructure($tableName, true);

        // assume that everything is up to date, until proven otherwise
        $tableStatus->status = self::TABLE_STATUS_UP_TO_DATE;

        foreach ($fields as $field => $fieldType) {
            $fieldStatus = new _FieldStatus();

            if (isset($structure[$field]) && is_array($structure[$field])) {
                $fieldStatus->exists = true;

//                $fieldType = $this->table->_getFieldType($field);
                $fieldAttributes = $this->table->_getFieldAttributes($field);
                $fieldStatus = $this->checkFieldType($fieldType, $fieldAttributes, $structure[$field], $fieldStatus);

                $index = $this->table->_getFieldIndex($field);
                $fieldStatus = $this->checkIndex($index, $structure[$field], $fieldStatus);

                $fieldStatus = $this->checkForeignKeys($index, $structure[$field], $fieldStatus);
            } else {
                $tableStatus->exists = false;
            }

            $isUpToDate = $this->isFieldUpToDate($fieldStatus);
            if (!$isUpToDate) {
                $tableStatus->status = self::TABLE_STATUS_OUTDATED;
            }
            $tableStatus->fields[$field] = $fieldStatus;
        }
        return $tableStatus;
    }

    /**
     *
     */
    private function isFieldUpToDate(_FieldStatus $fieldStatus): bool
    {
        foreach ($fieldStatus as $parameterValue) {
            if (!$parameterValue) {
                return false;
            }
        }
        return true;
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
        $fieldStatus->type = strtolower($fieldType->type) === $dbFieldType['COLUMN_TYPE'];
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
//        $fieldStatus->indexKey = $index->key === $dbIndex['COLUMN_KEY'];
        $fieldStatus->indexType = $index->type === $dbIndex['INDEX_TYPE'];
        $fieldStatus->indexPrimary = !!$index->primary === ($dbIndex['CONSTRAINT_NAME'] === 'PRIMARY');
        // this may be not obvious, since in PHPJet we use 'unique' field, but in MySQL schema there's 'non-unique'
        // so if these params match, they actually mismatch
        // more obvious line - !(!$index->unique !== (bool)$dbIndex['Non_unique'])
        $fieldStatus->indexUnique = $index->unique === !$dbIndex['NON_UNIQUE'];
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
        $fieldStatus->foreignKeyTable = (string)$index->foreignKeyField->table === $dbForeignKey['REFERENCED_TABLE_NAME'];
        $fieldStatus->foreignKeyField = (string)$index->foreignKeyField->field === $dbForeignKey['REFERENCED_COLUMN_NAME'];
        return $fieldStatus;
    }
}