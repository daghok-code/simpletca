<?php

namespace Febis\SimpleTca\Data\TsConfig;

use Febis\SimpleTca\Exception\TsConfigExistsException;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Package\Cache\PackageDependentCacheIdentifier;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TsConfigData
{
    /** @var FceItem[] $fceItems */
    protected array $fceItems = [];
    /** @var FceGroup[] $fceGroups */
    protected array $fceGroups = [];

    protected static ?FrontendInterface $codeCache = null;

    public function __construct($noCache = false)
    {
        if ($noCache === false) {
            $this->fetchFromCache();
        }
    }

    public function hasFceItem(string $identifier): bool
    {
        return isset($this->fceItems[$identifier]);
    }

    /**
     * @throws TsConfigExistsException
     */
    public function addFceItem(string $identifier, FceItem $fceItem, bool $override = false): static
    {
        if ($this->hasFceItem($identifier) && !$override) {
            throw new TsConfigExistsException($identifier, false);
        } else {
            $this->fceItems[$identifier] = $fceItem;
        }

        $this->updateCache();

        return $this;
    }

    public function removeFceItem(string $identifier): static
    {
        if (null !== $this->fceItems[$identifier] ?? null) {
            unset($this->fceItems[$identifier]);
        }

        $this->updateCache();

        return $this;
    }

    public function hasFceGroup(string $identifier): bool
    {
        return isset($this->fceGroups[$identifier]);
    }

    public function addFceGroup(string $identifier, FceGroup $fceGroup, bool $override = false): static
    {
        if ($this->hasFceGroup($identifier) && !$override) {
            throw new TsConfigExistsException($identifier, true);
        } else {
            $this->fceGroups[$identifier] = $fceGroup;
        }

        $this->updateCache();

        return $this;
    }

    public function removeFceGroup(string $identifier): static
    {
        if (null !== $this->fceGroups[$identifier] ?? null) {
            unset($this->fceGroups[$identifier]);
        }

        $this->updateCache();

        return $this;
    }

    public function getFullTsConfig(): ?string
    {
        $tsConfig = '';

        foreach ($this->fceGroups as $group) {
            $tsConfig .= $group->generateTsConfig();
        }

        foreach ($this->fceItems as $fce) {
            $tsConfig .= $fce->generateTsConfig();
        }

        return $tsConfig;
    }

    protected static function getBaseTcaCacheIdentifier()
    {
        return (new PackageDependentCacheIdentifier(GeneralUtility::makeInstance(PackageManager::class)))
            ->withPrefix('simpletca_tsconfig')->toString();
    }

    protected function createBaseTcaCacheFile(FrontendInterface $codeCache): void
    {
        $codeCache->set(
            static::getBaseTcaCacheIdentifier(),
            'return '
            . var_export(['tsConfigData' => $this], true)
            . ';'
        );
    }

    protected static function getCodeCache(): FrontendInterface
    {
        if (null === static::$codeCache) {
            static::$codeCache = GeneralUtility::makeInstance(CacheManager::class)->getCache('simpletca_tsconfig');
        }

        return static::$codeCache;
    }

    protected function updateCache(): void
    {
        $codeCache = static::getCodeCache();
        $this->createBaseTcaCacheFile($codeCache);
    }

    public static function __set_state(array $data)
    {
        $newObj = new static(true);
        $newObj->fceGroups = $data['fceGroups'];
        $newObj->fceItems = $data['fceItems'];
        return $newObj;
    }

    protected function fetchFromCache(): void
    {
        $codeCache = static::getCodeCache();
        $cacheIdentifier = static::getBaseTcaCacheIdentifier();
        $cacheData = $codeCache->require($cacheIdentifier);
        if ($cacheData) {
            /** @var static $tsConfigDataObj */
            $tsConfigDataObj = $cacheData['tsConfigData'];
            $this->fceItems = $tsConfigDataObj->fceItems;
            $this->fceGroups = $tsConfigDataObj->fceGroups;
        }
    }

}
