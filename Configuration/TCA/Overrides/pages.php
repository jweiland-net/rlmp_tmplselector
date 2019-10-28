<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$tempColumns = [
    'tx_rlmptmplselector_main_tmpl' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:rlmp_tmplselector/Resources/Private/Language/locallang_db.xlf:pages.tx_rlmptmplselector_main_tmpl',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                [
                    'LLL:EXT:rlmp_tmplselector/Resources/Private/Language/locallang_db.xlf:pages.tx_rlmptmplselector_main_tmpl.I.0',
                    '0',
                    'EXT:rlmp_tmplselector/Resources/Public/Icons/dummy_main.gif'
                ],
            ],
            'itemsProcFunc' => \JWeiland\RlmpTmplselector\Tca\AddFilesToSel::class . '->main',
        ],
    ],
    'tx_rlmptmplselector_ca_tmpl' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:rlmp_tmplselector/Resources/Private/Language/locallang_db.xlf:pages.tx_rlmptmplselector_ca_tmpl',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                [
                    'LLL:EXT:rlmp_tmplselector/Resources/Private/Language/locallang_db.xlf:pages.tx_rlmptmplselector_ca_tmpl.I.0',
                    '0',
                    'EXT:rlmp_tmplselector/Resources/Public/Icons/dummy_ca.gif'
                ],
            ],
            'itemsProcFunc' => \JWeiland\RlmpTmplselector\Tca\AddFilesToSelCa::class . '->main',
        ],
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $tempColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    'tx_rlmptmplselector_main_tmpl, tx_rlmptmplselector_ca_tmpl'
);
