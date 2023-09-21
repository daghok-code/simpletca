<?php

namespace Febis\SimpleTca\Shortcut;

/**
 * Shortcuts need to implement this interface to enable automatic generation of data processors
 */
interface DataProcessorInterface
{
    public function getDataProcessorType(): string;

    /**
     * Important: if you need to add a type for the configuration, add it like the following example
     *
     * TypoScript:
     * dataProcessing.20 = TYPO3\CMS\Frontend\DataProcessing\FilesProcessor
     * dataProcessing.20 {
     *   as = images
     * }
     *
     * Config array:
     * [
     *   'dataProcessing.20' => [
     *     '__type' => TYPO3\CMS\Frontend\DataProcessing\FilesProcessor,
     *     'as' => 'images',
     *   ]
     * ]
     */
    public function getDataProcessorConfig(string $fieldName): array;
}
