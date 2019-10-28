<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'JWeiland.rlmp_tmplselector',
    'TemplateSelector',
    'RLMP Template Selector'
);
