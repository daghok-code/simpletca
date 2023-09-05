<?php

namespace Febis\SimpleTca\Utility;

use Febis\SimpleTca\Exception\CallstackExtractionException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CallStackExtractor implements SingletonInterface
{
    protected ?array $tableList = null;

    /**
     * @throws CallstackExtractionException
     */
    public function extractFromCallstack(): array
    {
        $stackTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20);

        foreach ($stackTrace as $traceItem) {
            preg_match(
                '/[^\/]+\/([^\/]+)\/Configuration\/TCA\/(Overrides\/)?([^\.]+)\.php/',
                $traceItem['file'] ?? '',
                $fileMatch
            );

            if (!empty($fileMatch)) {
                $extkey = $fileMatch[1] ?? null;
                $tablename = $fileMatch[3] ?? null;

                $realTablename = $this->getRealTablename($tablename);
                return [$extkey, $realTablename];
            }
        }

        throw new CallstackExtractionException();
    }

    protected function getTableList(): array
    {
        if (null === $this->tableList) {
            $this->tableList = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable('tt_content')
                ->getConnection()
                ->prepare("SHOW TABLES;")
                ->executeQuery()
                ->fetchFirstColumn();

            sort($this->tableList);
        }

        return $this->tableList;
    }

    protected function getRealTablename(string $tablename): string
    {
        if (in_array($tablename, $this->getTableList(), true)) {
            return $tablename;
        }

        $partialMatch = array_filter($this->tableList, static function ($realTablename) use ($tablename) {
            preg_match("/^$realTablename.*$/", $tablename, $match);
            return isset($match[0]);
        });

        return reset($partialMatch) ?: $tablename;
    }
}
