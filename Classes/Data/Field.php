<?php

namespace Febis\SimpleTca\Data;

class Field
{
    public function __construct(
        public $type,
        public $default = null
    )
    {}

    public function getSQLDefaultValue(): string
    {
        if ($this->default === 'NULL') {
            return $this->default;
        } elseif ($this->default !== 0 && empty($this->default)) {
            return "'' NOT NULL";
        } else {
            return $this->default . ' NOT NULL';
        }
    }
}
