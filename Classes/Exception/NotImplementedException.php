<?php

namespace Febis\SimpleTca\Exception;

/** Exception thrown when the method is not yet implemented. */
class NotImplementedException extends SimpleTcaException
{
    public function __construct(
        string $methodName = "",
        int $code = 1672916415,
        ?\Throwable $previous = null
    ) {
        $message = "The method" . ($methodName !== '' ? " \"$methodName\"" : "") . " is not implemented yet.";
        parent::__construct($message, $code, $previous);
    }
}
