<?php

namespace Febis\SimpleTca\Data\Cacheable;

class BaseCacheable extends AbstractCacheable
{
    public function getTimestamp(): int
    {
        return time();
    }
}
