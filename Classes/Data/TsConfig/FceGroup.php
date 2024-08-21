<?php

namespace Febis\SimpleTca\Data\TsConfig;

use Febis\SimpleTca\Utility\TypoScriptHelper;

class FceGroup
{
    public function __construct(
        protected string $identifier = 'default',
        protected ?string $header = null,
        protected string $show = '*',
    ) {
    }

    protected function getHeader(): string
    {
        return $this->header ?? $this->identifier;
    }

    public function generateTsConfig(): string
    {
        $objectIdentifier = sprintf('mod.wizards.newContentElement.wizardItems.%s', $this->identifier);
        $object = [
            ['header', $this->getHeader()],
            ['show', $this->show],
        ];
        return TypoScriptHelper::objectToTextualRepresentation($objectIdentifier, $object);
    }

    public static function __set_state(array $data)
    {
        return new self(...$data);
    }
}
