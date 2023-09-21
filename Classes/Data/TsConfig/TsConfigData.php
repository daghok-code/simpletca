<?php

namespace Febis\SimpleTca\Data\TsConfig;

use Febis\SimpleTca\Exception\TsConfigExistsException;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Package\Cache\PackageDependentCacheIdentifier;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TsConfigData
{
    protected const CACHE_IDENTIFIER = 'simpletca_tsconfig';

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
     * @throws TsConfigExistsException|NoSuchCacheException
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
        if($this->hasFceItem($identifier)) {
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
        if ($this->hasFceGroup($identifier)) {
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

    protected static function getBaseTsConfigCacheIdentifier(): string
    {
        return (new PackageDependentCacheIdentifier(GeneralUtility::makeInstance(PackageManager::class)))
            ->withPrefix(static::CACHE_IDENTIFIER)->toString();
    }

    protected function createBaseTsConfigCacheFile(FrontendInterface $codeCache): void
    {
        $codeCache->set(
            static::getBaseTsConfigCacheIdentifier(),
            'return '
            . var_export(['tsConfigData' => $this], true)
            . ';'
        );
    }

    /**
     * @throws NoSuchCacheException
     */
    protected static function getCodeCache(): FrontendInterface
    {
        if (null === static::$codeCache) {
            static::$codeCache = GeneralUtility::makeInstance(CacheManager::class)->getCache(static::CACHE_IDENTIFIER);
        }

        return static::$codeCache;
    }

    /**
     * @throws NoSuchCacheException
     */
    protected function updateCache(): void
    {
        $codeCache = static::getCodeCache();
        $this->createBaseTsConfigCacheFile($codeCache);
    }

    public static function __set_state(array $data)
    {
        $newObj = new static(true);
        $newObj->fceGroups = $data['fceGroups'];
        $newObj->fceItems = $data['fceItems'];
        return $newObj;
    }

    /**
     * @throws NoSuchCacheException
     */
    protected function fetchFromCache(): void
    {
        $codeCache = static::getCodeCache();
        $cacheIdentifier = static::getBaseTsConfigCacheIdentifier();
        $cacheData = $codeCache->require($cacheIdentifier);
        if ($cacheData) {
            /** @var static $tsConfigDataObj */
            $tsConfigDataObj = $cacheData['tsConfigData'];
            $this->fceItems = $tsConfigDataObj->fceItems;
            $this->fceGroups = $tsConfigDataObj->fceGroups;
        }
    }

}
