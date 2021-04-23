<?php


namespace Jet\App\Engine\ActiveRecord;

/**
 * Class Field
 * @package Jet\App\Engine\ActiveRecord
 */
class Field
{
    private const FIELD_TYPE_BOOL = 'TINYINT(1)';
    private const FIELD_TYPE_INT = 'INT';
    private const FIELD_TYPE_VARCHAR = 'VARCHAR';
    private const FIELD_TYPE_TEXT = 'LONGTEXT';
    private const FIELD_TYPE_DATETIME = 'DATETIME';

    private const FIELD_INDEX_TYPE_BTREE = 'FIELD_INDEX_TYPE_BTREE';

    private const FIELD_FOREIGN_KEY_TYPE_CASCADE = 'FIELD_FOREIGN_KEY_TYPE_CASCADE';
    private const FIELD_FOREIGN_KEY_TYPE_RESTRICT = 'FIELD_FOREIGN_KEY_TYPE_RESTRICT';
    /**
     * @var string
     */
    private $type;
    /**
     * @var int
     */
    private $maxLength;
    /**
     * @var int
     */
    private $minlength;
    /**
     * @var string
     */
    private $index;
    /**
     * @var bool
     */
    private $primary;
    /**
     * @var bool
     */
    private $autoIncrement;
    /**
     * @var self
     */
    private $foreignKeyField;
    /**
     * @var string
     */
    private $foreignKeyType;
    /**
     * @var bool
     */
    private $notNull = true; // always true
    /**
     * @var mixed
     */
    private $_value;

    /**
     * Field constructor.
     * @param string $type
     * @param int $minlength
     * @param int $maxLength
     */
    public function __construct(string $type, int $minlength, int $maxLength)
    {
        $this->type = $type;
        $this->minlength = $minlength;
        $this->maxLength = $maxLength;
    }

    /**
     * @param int $maxLength
     * @return Field
     */
    public static function int(int $maxLength = 11): self
    {
        return new self(self::FIELD_TYPE_INT . "($maxLength)", 0, $maxLength);
    }

    /**
     * @return static
     */
    public static function bool(): self
    {
        return new self(self::FIELD_TYPE_BOOL, 0, 1);
    }

    /**
     * @param int $maxLength
     * @return static
     */
    public static function varchar(int $maxLength = 255): self
    {
        return new self(self::FIELD_TYPE_VARCHAR . "($maxLength)", 0, $maxLength);
    }

    /**
     * @param int $maxLength
     * @return static
     */
    public static function text(int $maxLength = 65536): self
    {
        return new self(self::FIELD_TYPE_TEXT, 0, $maxLength);
    }

    /**
     * @return static
     */
    public static function dateTime(): self
    {
        return new self(self::FIELD_TYPE_DATETIME, 0, 32);
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setIndex(string $type = self::FIELD_INDEX_TYPE_BTREE): self
    {
        $this->index = $type;
        return $this;
    }

    /**
     * @param bool $autoIncrement
     * @return $this
     */
    public function setPrimary(bool $autoIncrement = true): self
    {
        $this->primary = true;
        $this->autoIncrement = $autoIncrement;
        return $this;
    }

    /**
     * @param _FieldType $field
     * @param string $type
     * @return $this
     */
    public function setForeignKey(_FieldType $field, string $type = self::FIELD_FOREIGN_KEY_TYPE_CASCADE): self
    {
        $this->foreignKeyField = $field;
        $this->foreignKeyType = $type;
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
     * @return string
     */
    public function _getType(): string
    {
        return $this->type;
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