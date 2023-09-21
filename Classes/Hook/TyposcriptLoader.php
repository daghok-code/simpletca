<?php

namespace Febis\SimpleTca\Hook;

use Febis\SimpleTca\TcaGenerator;
use TYPO3\CMS\Core\TypoScript\TemplateService;

class TyposcriptLoader
{
    public function addGeneratedTypoScript(&$hookParameters, TemplateService $templateService): void
    {
        $setup = TcaGenerator::getTyposcriptData()->getFullTyposcript();
        $tstamp = TcaGenerator::getTyposcriptData()->getTimestamp();

        $rootLine = $hookParameters['rootLine'] ?? null;
        if (!is_array($rootLine) || empty($rootLine)) {
            return;
        }

        foreach ($rootLine as $pageRecord) {
            $row = [
                'config' => $setup,
                'constants' => '',
                'nextLevel' => 0,
                'static_file_mode' => 1,
                'tstamp' => $tstamp,
                'uid' => 'ext_simpletca_' . (int)$pageRecord['uid'],
                'title' => 'SimpleTCA for page ' . (int)$pageRecord['uid'],
                'root' => false,
            ];
            $templateService->processTemplate(
                $row,
                'ext_simpletca',
                (int)$pageRecord['uid'],
                'ext_simpletca',
            );
        }
    }
}
