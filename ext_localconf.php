<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'JWeiland.rlmp_tmplselector',
    'TemplateSelector',
    [
        'TemplateSelector' => 'show',
    ],
    // non-cacheable actions
    [
        'TemplateSelector' => '',
    ]
);
