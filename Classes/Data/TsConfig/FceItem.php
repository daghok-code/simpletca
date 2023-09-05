<?php

namespace Febis\SimpleTca\Data\TsConfig;

use Febis\SimpleTca\Exception\TsConfigExistsException;

class FceItem
{
    /**
     * @throws TsConfigExistsException
     */
    public function __construct(
        protected string $identifier,
        protected string $groupIdentifier,
        protected string $iconIdentifier = 'default-not-found',
        protected ?string $title = null,
    ) {
    }

    protected function getTitle(): string
    {
        return $this->title ?? $this->identifier;
    }

    public function generateTsConfig(): string
    {
        return "
            mod.wizards.newContentElement.wizardItems." . $this->groupIdentifier . " {
              elements {
                " . $this->identifier . " {
                  iconIdentifier = " . $this->iconIdentifier . "
                  title = " . $this->getTitle() . "
                  tt_content_defValues {
                    CType = " . $this->identifier . "
                  }
                }
              }

              show := addToList(" . $this->identifier . ")
            }
        ";
    }

    public static function __set_state(array $data)
    {
        return new static(...$data);
    }
}
