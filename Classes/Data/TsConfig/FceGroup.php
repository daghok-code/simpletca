<?php

namespace Febis\SimpleTca\Data\TsConfig;

class FceGroup
{
    public function __construct(
        protected string $identifier = 'default',
        protected ?string $header = null,
        protected string $show = '*'
    ) {
    }

    protected function getHeader(): string
    {
        return $this->header ?? $this->identifier;
    }

    public function generateTsConfig(): string
    {
        return "
            mod {
                wizards.newContentElement.wizardItems." . $this->identifier . " {
                    header = " . $this->getHeader() . "
                    show = " . $this->show . "
                }
            }
        ";
    }

    public static function __set_state(array $data)
    {
        return new static(...$data);
    }
}
