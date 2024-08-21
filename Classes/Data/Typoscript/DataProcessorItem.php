<?php

namespace Febis\SimpleTca\Data\Typoscript;

class DataProcessorItem
{
    public function __construct(
        protected string $type,
        protected array $config,
    ) {
    }

    public function getProcessorClass(): string
    {
        return $this->type;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public static function __set_state(array $data)
    {
        return new self(...$data);
    }
}
