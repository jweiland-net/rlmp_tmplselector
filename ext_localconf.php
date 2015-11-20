<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'JWeiland.' . $_EXTKEY,
    'TemplateSelector',
    array(
        'TemplateSelector' => 'show',
    ),
    // non-cacheable actions
    array(
        'TemplateSelector' => '',
    )
);
