<?php

namespace Febis\SimpleTca\Data;

use Febis\SimpleTca\Exception\CallstackExtractionException;
use Febis\SimpleTca\Utility\CallStackExtractor;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Config implements SingletonInterface
{
    protected string $defaultFile = 'contentelements';

    /** @var TcaFileConfig[] $cachedTcaFileConfigs */
    protected array $cachedTcaFileConfigs = [];

    protected ?TcaFileConfig $currentTcaFileConfig = null;

    public function ll(): string
    {
        return $this->currentTcaFileConfig->llFullOverride ??
            'LLL:EXT:' .
            $this->getExtKey() .
            '/Resources/Private/Language/' .
            ($this->currentTcaFileConfig->llFile ?? $this->defaultFile) .
            '.xlf:';
    }

    public function setDefaultLocalizationFile(string $filename): void
    {
        $this->defaultFile = $filename;
    }

    public function setLlFile(string $llFile): void
    {
        $this->currentTcaFileConfig->llFile = $llFile;
    }

    public function setLlFullOverride(?string $llFullOverride): void
    {
        $this->currentTcaFileConfig->llFullOverride = $llFullOverride;
    }

    public function getTablename(): string
    {
        return $this->currentTcaFileConfig->getTablename();
    }

    public function getExtkey(): string
    {
        return $this->currentTcaFileConfig->getExtkey();
    }

    public function setTablename(?string $tablename): void
    {
        $this->currentTcaFileConfig->setTablename($tablename);
    }

    /**
     * @throws CallstackExtractionException
     */
    public function switchFileConfig(): static
    {
        [$extkey, $filename] = GeneralUtility::makeInstance(CallStackExtractor::class)->extractExtkeyAndFilename();

        $cacheName = $extkey . '-' . $filename;

        if (!isset($this->cachedTcaFileConfigs[$cacheName])) {
            $this->cachedTcaFileConfigs[$cacheName] = GeneralUtility::makeInstance(TcaFileConfig::class);
            $this->cachedTcaFileConfigs[$cacheName]->injectRuntimeData();
        }

        $this->currentTcaFileConfig = $this->cachedTcaFileConfigs[$cacheName];

        return $this;
    }
}
