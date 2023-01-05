<?php

namespace Febis\SimpleTca\Data;

use Febis\SimpleTca\Exception\NotImplementedException;
use TYPO3\CMS\Core\SingletonInterface;

class Config implements SingletonInterface
{
    protected string $llFile = 'contentelements';
    protected ?string $llFullOverride = null; // eg. LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:

    public function ll(): string
    {
        return $this->llFullOverride ??
            'LLL:EXT:' . $this->getExtKey() . '/Resources/Private/Language/' . $this->llFile . '.xlf:';
    }

    public function setLlFile(string $llFile): void
    {
        $this->llFile = $llFile;
    }

    public function getLlFile(): string
    {
        return $this->llFile;
    }

    public function getLlFullOverride(): ?string
    {
        return $this->llFullOverride;
    }

    public function setLlFullOverride(?string $llFullOverride): void
    {
        $this->llFullOverride = $llFullOverride;
    }

    protected function getExtKey(): string
    {
        // TODO: search for a way to get extension key on runtime from calling context (the TCA file which generates TCA)
        throw new NotImplementedException('getExtKey');
    }
}
