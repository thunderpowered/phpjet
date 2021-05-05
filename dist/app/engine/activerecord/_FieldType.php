<?php


namespace Jet\App\Engine\ActiveRecord;

/**
 * Class _FieldType
 * @package Jet\App\Engine\ActiveRecord
 */
class _FieldType
{
    /**
     * @var string
     */
    public $table; // table name
    /**
     * @var string
     */
    public $field; // field name
    /**
     * @var string
     */
    public $type;
    /**
     * @var int
     */
    public $minLength;
    /**
     * @var int
     */
    public $maxLength;

    /**
     * _FieldType constructor.
     * @param string $type
     * @param int $minLength
     * @param int $maxLength
     */
    public function __construct(string $type, int $minLength, int $maxLength)
    {
        $this->type = $type;
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
    }
}