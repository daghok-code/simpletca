<?php

namespace Febis\SimpleTca\Exception;

/** Exception thrown when the table could not be parsed. */
class TableNotParsedException extends SimpleTcaException
{
    public function __construct(
        string $message = "",
        int $code = 1672744036,
        ?\Throwable $previous = null
    ) {
        $message = "" !== $message
            ? $message
            : "There is no \"Configuration/TCA\" file in calling backtrace found.
            Please set the tablename yourself by calling \"setParseTablename\"";
        parent::__construct($message, $code, $previous);
    }
}
