<?php

namespace Febis\SimpleTca\Debug;

interface DebugAwareInterface
{
    public function setDebug(DebugInterface $debug): void;

    public function addDebugMessage(string $message, string $key = null): void;
}
