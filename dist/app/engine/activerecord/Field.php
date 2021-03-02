<?php


namespace Jet\App\Engine\ActiveRecord;

/**
 * Class Field
 * @package Jet\App\Engine\ActiveRecord
 */
class Field
{
    private const FIELD_TYPE_BOOL = 'FIELD_TYPE_BOOL';
    private const FIELD_TYPE_INT = 'FIELD_TYPE_INT';
    private const FIELD_TYPE_VARCHAR = 'FIELD_TYPE_VARCHAR';
    private const FIELD_TYPE_TEXT = 'FIELD_TYPE_TEXT';
    /**
     * @var string
     */
    public $type;
    /**
     * @var int
     */
    public $maxLength;
    /**
     * @var int
     */
    public $minlength;

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
        return new self(self::FIELD_TYPE_INT, 11, $maxLength);
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
        return new self(self::FIELD_TYPE_VARCHAR, 0, $maxLength);
    }

    /**
     * @param int $maxLength
     * @return static
     */
    public static function text(int $maxLength = 65536): self
    {
        return new self(self::FIELD_TYPE_TEXT, 0, $maxLength);
    }
}