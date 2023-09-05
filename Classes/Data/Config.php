<?php

namespace Febis\SimpleTca\Data;

use Febis\SimpleTca\Exception\CallstackExtractionException;
use Febis\SimpleTca\Utility\CallStackExtractor;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Config implements SingletonInterface
{
    protected string $extkey = '';
    protected string $tablename = '';
    protected bool $overrideTablename = false;
    protected ?string $llFile = null;
    protected ?string $llFullOverride = null; // eg. LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:
    protected string $defaultFile = 'contentelements';

    public function ll(): string
    {
        return $this->llFullOverride ??
            'LLL:EXT:' .
            $this->getExtKey() .
            '/Resources/Private/Language/' .
            ($this->llFile ?? $this->defaultFile) .
            '.xlf:';
    }

    public function setDefaultLocalizationFile(string $filename): void
    {
        $this->defaultFile = $filename;
    }

    public function setLlFile(string $llFile): void
    {
        $this->llFile = $llFile;
    }

    public function resetLlFile(): void
    {
        $this->llFile = null;
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

    /**
     * @return string
     */
    public function getTablename(): string
    {
        return $this->tablename;
    }

    /**
     * @return string
     */
    public function getExtkey(): string
    {
        return $this->extkey;
    }

    /**
     * @param string|null $tablename
     */
    public function setTablename(?string $tablename): void
    {
        $this->tablename = $tablename;
        $this->overrideTablename = true;
    }

    public function resetTablename(): void
    {
        $this->tablename = '';
        $this->overrideTablename = false;
    }

    /**
     * @return bool
     */
    public function isOverrideTablename(): bool
    {
        return $this->overrideTablename;
    }

    /**
     * @throws CallstackExtractionException
     */
    public function injectRuntimeData(): void
    {
        [$extkey, $tablename] = GeneralUtility::makeInstance(CallStackExtractor::class)->extractFromCallstack();

        $this->extkey = $extkey ?? '';
        if (false === $this->isOverrideTablename()) {
            $this->tablename = $tablename ?? '';
        }
    }
}
