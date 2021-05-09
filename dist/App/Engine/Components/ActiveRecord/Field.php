<?php


namespace Jet\App\Engine\Components\ActiveRecord;

use Jet\App\Engine\Exceptions\CoreException;

/**
 * Class Field
 * @package Jet\App\Engine\Components\ActiveRecord
 */
class Field
{
    // i didn't want to make them public, but i need it in Checked and Builder classes
    public const FIELD_INDEX_KEY_PRIMARY = 'PRI';
    public const FIELD_INDEX_KEY_MULTI = 'MUL';
    public const FIELD_INDEX_KEY_UNIQUE = 'UNI';

    public const FIELD_INDEX_TYPE_BTREE = 'BTREE';

    private const FIELD_TYPE_BOOL = 'TINYINT(1)';
    private const FIELD_TYPE_INT = 'INT';
    private const FIELD_TYPE_VARCHAR = 'VARCHAR';
    private const FIELD_TYPE_TEXT = 'LONGTEXT';
    private const FIELD_TYPE_DATETIME = 'DATETIME';

    private const FIELD_FOREIGN_KEY_TYPE_CASCADE = 'FIELD_FOREIGN_KEY_TYPE_CASCADE';
    private const FIELD_FOREIGN_KEY_TYPE_RESTRICT = 'FIELD_FOREIGN_KEY_TYPE_RESTRICT';
    /**
     * @var _FieldType
     */
    private $type;
    /**
     * @var mixed
     */
    private $_value;
    /**
     * @var _FieldAttributes
     */
    private $attributes;
    /**
     * @var _FieldIndex
     */
    private $index;

    /**
     * Field constructor.
     * @param _FieldType $type
     */
    public function __construct(_FieldType $type)
    {
        $this->type = $type;
        $this->attributes = new _FieldAttributes();
        $this->index = new _FieldIndex();
    }

    /**
     * @param int $maxLength
     * @return Field
     */
    public static function int(int $maxLength = 11): self
    {
        return new self(
            new _FieldType(self::FIELD_TYPE_INT . "($maxLength)", 0, $maxLength)
        );
    }

    /**
     * @return static
     */
    public static function bool(): self
    {
        return new self(
            new _FieldType(self::FIELD_TYPE_BOOL, 0, 1)
        );
    }

    /**
     * @param int $maxLength
     * @return static
     */
    public static function varchar(int $maxLength = 255): self
    {
        return new self(
            new _FieldType(self::FIELD_TYPE_VARCHAR . "($maxLength)", 0, $maxLength)
        );
    }

    /**
     * @param int $maxLength
     * @return static
     */
    public static function text(int $maxLength = 65536): self
    {
        return new self(
            new _FieldType(self::FIELD_TYPE_TEXT, 0, $maxLength)
        );
    }

    /**
     * @return static
     */
    public static function dateTime(): self
    {
        return new self(
            new _FieldType(self::FIELD_TYPE_DATETIME, 0, 32)
        );
    }

    /**
     * @param string $type
     * @param bool $unique
     * @return $this
     */
    public function setIndex(bool $unique = false, string $type = self::FIELD_INDEX_TYPE_BTREE): self
    {
        // that's a bit messy
        $this->index->index = true;
        $this->index->type = $type;
        $this->index->unique = $unique;
        return $this;
    }

    /**
     * @param _FieldType $field
     * @param string $type
     * @return $this
     */
    public function setForeignKey(_FieldType $field, string $type = self::FIELD_FOREIGN_KEY_TYPE_CASCADE): self
    {
        // foreign key requires index
        // actually if we just create a new foreign key, MySQL automatically creates an index
        // so there's actually no need to create it explicitly
        // but since we want to store complete state of the table in class properties, there's situation when states of actual table in database and class are different which complicates the comparison and may cause other unwanted effects
        // so i think it's better to set index, not a big deal anyway
        $this->index->index = true;
        $this->index->type = self::FIELD_INDEX_TYPE_BTREE;

        $this->index->foreignKey = true;
        $this->index->foreignKeyField = $field;
        $this->index->foreignKeyType = $type;
        return $this;
    }

    /**
     * @param bool $autoIncrement
     * @return $this
     */
    public function setPrimary(bool $autoIncrement = true): self
    {
        // almost the same story as with foreign key
        // we have either make an additional check for primary while comparing, or just set the index
        // for me this method seems more accurate and simple, since primary key is formally an index
        $this->index->index = true;
        $this->index->type = self::FIELD_INDEX_TYPE_BTREE;

        $this->index->primary = true;
        $this->index->unique = true;
        $this->attributes->autoIncrement = $autoIncrement;
        return $this;
    }

    /**
     * @param $value
     */
    public function _setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * @return mixed
     */
    public function _getValue()
    {
        return $this->_value;
    }

    /**
     * @return _FieldType
     */
    public function _getType(): _FieldType
    {
        return $this->type;
    }

    /**
     * @return _FieldAttributes
     */
    public function _getAttributes(): _FieldAttributes
    {
        return $this->attributes;
    }

    /**
     * @return _FieldIndex
     */
    public function _getIndex(): _FieldIndex
    {
        return $this->index;
    }

    /**
     * @return bool
     */
    public function _hasValue(): bool
    {
        return !!$this->_value;
    }

    /**
     * @return string
     * temporary
     */
    public function __toString(): string
    {
        return (string)$this->_value;
    }
}