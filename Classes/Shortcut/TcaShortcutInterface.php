<?php

namespace Febis\SimpleTca\Shortcut;

/**
 * To enable auto
 */
interface TcaShortcutInterface
{
    public function build(?string $label = null): array;

    public function withArguments(array $args): static;
}
