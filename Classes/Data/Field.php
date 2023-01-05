<?php

namespace Febis\SimpleTca\Data;

class Field
{
    public function __construct(
        public $type,
        public $default = null
    )
    {}
}
