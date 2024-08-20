<?php

namespace Febis\SimpleTca\Debug;

interface DebugInterface
{
    public function add(mixed $debugData, string|int|null $key = null): void;

    public function get(): array;
}
