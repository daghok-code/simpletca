<?php

namespace Febis\SimpleTca\Data\Typoscript;

use Febis\SimpleTca\Exception\TyposcriptExistsException;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Package\Cache\PackageDependentCacheIdentifier;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TyposcriptData
{
    protected const CACHE_IDENTIFIER = 'simpletca_typoscript';

    public const BASE_PREFIX = 'lib.tx_simpletca.contentElement';
    public const BASE_DEFAULT = self::BASE_PREFIX . '._default';

    protected int $tstamp = 0;

    /** @var FceItem[] $fceItems */
    protected array $fceItems = [];

    /** @var BaseFceItem[] $baseFceItems */
    protected array $baseFceItems = [];

    protected static ?FrontendInterface $codeCache = null;

    /**
     * @throws NoSuchCacheException
     */
    public function __construct($noCache = false)
    {
        if ($noCache === false) {
            $this->fetchFromCache();
        }
    }

    public function getTimestamp(): int
    {
        return $this->tstamp;
    }

    public function getBaseFceItemFor(string $extKey): ?string
    {
        return $this->baseFceItems[$extKey]?->getObjectName() ?? null;
    }

    public function hasBaseFceItemFor(string $extKey): bool
    {
        return isset($this->baseFceItems[$extKey]);
    }

    /**
     * @throws NoSuchCacheException
     */
    public function addBaseFceItemFor(string $extKey): static
    {
        if (!$this->hasBaseFceItemFor($extKey)) {
            $this->baseFceItems[$extKey] = new BaseFceItem($extKey);
        }

        $this->updateCache();

        return $this;
    }

    public function hasFceItem(string $identifier): bool
    {
        return isset($this->fceItems[$identifier]);
    }

    /**
     * @throws TyposcriptExistsException|NoSuchCacheException
     */
    public function addFceItem(string $identifier, FceItem $fceItem, bool $override = false): static
    {
        if ($this->hasFceItem($identifier) && !$override) {
            throw new TyposcriptExistsException($identifier);
        } else {
            $this->fceItems[$identifier] = $fceItem;
        }

        $this->updateCache();

        return $this;
    }

    public function removeFceItem(string $identifier): static
    {
        if ($this->hasFceItem($identifier)) {
            unset($this->fceItems[$identifier]);
        }

        $this->updateCache();

        return $this;
    }

    public function getFullTyposcript(): ?string
    {
        $typoscript = [];

        foreach ($this->baseFceItems as $baseItem) {
            $typoscript[] = $baseItem->generateTyposcript();
        }

        foreach ($this->fceItems as $fceItem) {
            $typoscript[] = $fceItem->generateTyposcript();
        }

        return join("\n", $typoscript);
    }

    protected static function getBaseTyposcriptCacheIdentifier(): string
    {
        return (new PackageDependentCacheIdentifier(GeneralUtility::makeInstance(PackageManager::class)))
            ->withPrefix(static::CACHE_IDENTIFIER)->toString();
    }

    protected function createBaseTyposcriptCacheFile(FrontendInterface $codeCache): void
    {
        $this->tstamp = time();
        $codeCache->set(
            static::getBaseTyposcriptCacheIdentifier(),
            'return '
            . var_export(['typoscriptData' => $this], true)
            . ';',
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
        $this->createBaseTyposcriptCacheFile($codeCache);
    }

    public static function __set_state(array $data)
    {
        $newObj = new static(true);
        $newObj->baseFceItems = $data['baseFceItems'];
        $newObj->fceItems = $data['fceItems'];
        $newObj->tstamp = $data['tstamp'];
        return $newObj;
    }

    /**
     * @throws NoSuchCacheException
     */
    protected function fetchFromCache(): void
    {
        $codeCache = static::getCodeCache();
        $cacheIdentifier = static::getBaseTyposcriptCacheIdentifier();
        $cacheData = $codeCache->require($cacheIdentifier);
        if ($cacheData) {
            /** @var static $typoscriptObject */
            $typoscriptObject = $cacheData['typoscriptData'];
            $this->baseFceItems = $typoscriptObject->baseFceItems;
            $this->fceItems = $typoscriptObject->fceItems;
            $this->tstamp = $typoscriptObject->tstamp;
        }
    }
}
