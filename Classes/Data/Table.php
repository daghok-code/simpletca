<?php

namespace Febis\SimpleTca\Data;

class Table
{
    /** @var Field[] $fields */
    protected array $fields = [];

    /**
     * @param Field[] $fields
     */
    public function __construct(array $fields)
    {
        foreach ($fields as $identifier => $field) {
            $this->addField($field, $identifier);
        }
    }

    public function hasField(string $identifier): bool
    {
        return isset($this->fields[$identifier]);
    }

    public function addField(Field $field, string $identifier): static
    {
        if ($this->hasField($identifier)) {
            $this->replaceField($field, $identifier);
        } else {
            $this->fields[$identifier] = $field;
        }

        return $this;
    }

    public function replaceField(Field $field, string $identifier): static
    {
        $this->fields[$identifier] = $field;

        return $this;
    }

    public function removeField(string $identifier): static
    {
        if (isset($this->fields[$identifier])) {
            unset($this->fields[$identifier]);
        }

        return $this;
    }

    public function getField(string $identifier): ?Field
    {
        return $this->fields[$identifier] ?? null;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function clearFields(): static
    {
        $this->fields = [];

        return $this;
    }

    public function mergeWithOverrideTable(Table $toAppendWithPrio): static
    {
        foreach ($toAppendWithPrio->getFields() as $fieldIdentifier => $field) {
            $this->addField($field, $fieldIdentifier);
        }

        return $this;
    }
}
