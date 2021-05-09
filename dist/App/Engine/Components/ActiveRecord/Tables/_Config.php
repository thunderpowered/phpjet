<?php

namespace Jet\App\Engine\Components\ActiveRecord\Tables;

use Jet\App\Engine\Components\ActiveRecord\Field;
use Jet\App\Engine\Components\ActiveRecord\Table;

/**
 * Class _Config
 * @package Jet\App\Engine\Components\ActiveRecord\Tables
 */
class _Config extends Table
{
    /**
     * @var Field
     */
    protected $id;
    /**
     * @var Field
     */
    protected $domain;
    /**
     * @var Field
     * @deprecated
     */
    protected $admin_domain;

    /**
     * _Config constructor.
     * @param bool $loaded
     */
    public function __construct(bool $loaded = false)
    {
        parent::__construct($loaded);
        $this->id = Field::int()->setPrimary();
        $this->domain = Field::varchar()->setIndex();
        $this->admin_domain = Field::varchar()->setIndex();
    }
}