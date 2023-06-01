<?php

namespace Febis\SimpleTca\Shortcut;

/**
 * To enable auto
 */
interface TcaShortcutInterface
{
    public function build(): array;

    public function withArguments(array $args): static;
}
