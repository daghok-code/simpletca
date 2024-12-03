<?php

namespace Febis\SimpleTca\Data;

use TYPO3\CMS\Core\SingletonInterface;

class TcaDefinitionData implements SingletonInterface
{
    /** @var Table[] $tables */
    protected array $tables = [];

    public function hasTable(string $identifier): bool
    {
        return isset($this->tables[$identifier]);
    }

    public function addTable(Table $table, string $identifier): static
    {
        if ($this->hasTable($identifier)) {
            $this->tables[$identifier]->mergeWithOverrideTable($table);
        } else {
            $this->tables[$identifier] = $table;
        }

        return $this;
    }

    public function removeTable(string $identifier): static
    {
        if (isset($this->tables[$identifier])) {
            unset($this->tables[$identifier]);
        }

        return $this;
    }

    public function getTable(string $identifier): ?Table
    {
        return $this->tables[$identifier] ?? null;
    }

    public function getTables(): array
    {
        return $this->tables;
    }

    public function clearTables(): static
    {
        $this->tables = [];

        return $this;
    }
}
