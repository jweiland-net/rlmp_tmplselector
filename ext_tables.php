<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'JWeiland.' . $_EXTKEY,
	'TemplateSelector',
	'RLMP Template Selector'
);

$tempColumns = array (
	'tx_rlmptmplselector_main_tmpl' => array (
		'exclude' => 1,		
		'label' => 'LLL:EXT:rlmp_tmplselector/locallang_db.php:pages.tx_rlmptmplselector_main_tmpl',		
		'config' => array (
			'type' => 'select',
			'items' => array (
				array('LLL:EXT:rlmp_tmplselector/locallang_db.php:pages.tx_rlmptmplselector_main_tmpl.I.0', '0', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('rlmp_tmplselector') . 'Resources/Public/Icons/dummy_main.gif'),
			),
			'itemsProcFunc' => 'JWeiland\\RlmpTmplselector\\Tca\\AddFilesToSel->main',
		)
	),
	'tx_rlmptmplselector_ca_tmpl' => array (		
		'exclude' => 1,		
		'label' => 'LLL:EXT:rlmp_tmplselector/locallang_db.php:pages.tx_rlmptmplselector_ca_tmpl',
		'config' => array (
			'type' => 'select',
			'items' => array (
				array('LLL:EXT:rlmp_tmplselector/locallang_db.php:pages.tx_rlmptmplselector_ca_tmpl.I.0', '0', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('rlmp_tmplselector') . 'Resources/Public/Icons/dummy_ca.gif'),
			),
			'itemsProcFunc' => 'JWeiland\\RlmpTmplselector\\Tca\\AddFilesToSelCa->main',
		)
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $tempColumns, 1);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('pages', 'tx_rlmptmplselector_main_tmpl;;;;1-1-1, tx_rlmptmplselector_ca_tmpl');