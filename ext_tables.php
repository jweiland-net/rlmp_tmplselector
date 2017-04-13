<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'JWeiland.' . $_EXTKEY,
    'TemplateSelector',
    'RLMP Template Selector'
);

if (version_compare(TYPO3_branch, '7.6', '>')) {
    $extRelPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('rlmp_tmplselector');
} else {
    $extRelPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('rlmp_tmplselector');
}

$tempColumns = array (
    'tx_rlmptmplselector_main_tmpl' => array (
        'exclude' => 1,
        'label' => 'LLL:EXT:rlmp_tmplselector/Resources/Private/Language/locallang_db.xlf:pages.tx_rlmptmplselector_main_tmpl',
        'config' => array (
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => array (
                array(
                    'LLL:EXT:rlmp_tmplselector/Resources/Private/Language/locallang_db.xlf:pages.tx_rlmptmplselector_main_tmpl.I.0',
                    '0',
                    $extRelPath . 'Resources/Public/Icons/dummy_main.gif'
                ),
            ),
            'itemsProcFunc' => 'JWeiland\\RlmpTmplselector\\Tca\\AddFilesToSel->main',
        ),
    ),
    'tx_rlmptmplselector_ca_tmpl' => array (
        'exclude' => 1,
        'label' => 'LLL:EXT:rlmp_tmplselector/Resources/Private/Language/locallang_db.xlf:pages.tx_rlmptmplselector_ca_tmpl',
        'config' => array (
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => array (
                array(
                    'LLL:EXT:rlmp_tmplselector/Resources/Private/Language/locallang_db.xlf:pages.tx_rlmptmplselector_ca_tmpl.I.0',
                    '0',
                    $extRelPath . 'Resources/Public/Icons/dummy_ca.gif'
                ),
            ),
            'itemsProcFunc' => 'JWeiland\\RlmpTmplselector\\Tca\\AddFilesToSelCa->main',
        ),
    ),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $tempColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    'tx_rlmptmplselector_main_tmpl;;;;1-1-1, tx_rlmptmplselector_ca_tmpl'
);
