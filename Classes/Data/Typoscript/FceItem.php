<?php

namespace Febis\SimpleTca\Data\Typoscript;

use Febis\SimpleTca\Utility\TypoScriptHelper;

class FceItem
{
    /**
     * @param string $identifier Identifier like itl_examplece (generated as tt_content.itl_examplece)
     * @param string|null $templateName if not set, the identifier in UpperCamelCase will be used as template file name
     * @param string $baseElement base FLUIDTEMPLATE element from where content element is derived
     * @param DataProcessorItem[] $dataProcessors predefined data processors (probably for columns types image,
     *  file, relations...)
     */
    public function __construct(
        protected string $identifier,
        protected ?string $templateName = null,
        protected string $baseElement = TyposcriptDataHandling::BASE_DEFAULT,
        protected array $dataProcessors = [],
    ) {
        $this->templateName = $this->templateName ?? TypoScriptHelper::snakeToCamel($this->identifier);
    }

    public function generateTyposcript(): string
    {
        $tsObjectName = sprintf('tt_content.%s', $this->identifier);

        $tsObject = [
            ['templateName', $this->templateName]
        ];

        if (false === empty($this->dataProcessors)) {
            $counter = 0;
            $dataProcessors = [];
            foreach ($this->dataProcessors as $dataProcessor) {
                $counter += TypoScriptHelper::TYPOSCRIPT_COUNTING;
                $dataProcessors[] = [$counter, $dataProcessor->getProcessorClass()];
                $dataProcessors[] = [$counter, $dataProcessor->getConfig()];
            }
            $tsObject[] = ['dataProcessing', $dataProcessors];
        }

        $typoscript = [];
        $typoscript[] = sprintf("%s =< %s", $tsObjectName, $this->baseElement);
        $typoscript[] = TypoScriptHelper::objectToTextualRepresentation($tsObjectName, $tsObject);

        return join("\n", $typoscript);
    }

    public static function __set_state(array $data)
    {
        return new self(...$data);
    }
}
