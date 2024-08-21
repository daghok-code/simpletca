<?php

namespace Febis\SimpleTca\Data;

use Febis\SimpleTca\Exception\CallstackExtractionException;
use Febis\SimpleTca\Utility\CallStackExtractor;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TcaFileConfig
{
    public ?string $llFile = null;

    public ?string $llFullOverride = null; // eg. LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:

    protected string $extkey = '';

    protected string $tablename = '';

    protected bool $overrideTablename = false;

    public function getExtkey(): string
    {
        return $this->extkey;
    }

    public function getTablename(): string
    {
        return $this->tablename;
    }

    public function setTablename(?string $tablename): void
    {
        $this->tablename = $tablename;
        $this->overrideTablename = $tablename !== null && $tablename !== '' && $tablename !== '0';
    }

    public function resetTablename(): void
    {
        $this->tablename = '';
        $this->overrideTablename = false;
    }

    /**
     * @throws CallstackExtractionException
     */
    public function injectRuntimeData(): void
    {
        [$extkey, $tablename] = GeneralUtility::makeInstance(CallStackExtractor::class)->extractExtkeyAndTablename();

        $this->extkey = $extkey ?? '';
        if (false === $this->overrideTablename) {
            $this->tablename = $tablename ?? '';
        }
    }
}
