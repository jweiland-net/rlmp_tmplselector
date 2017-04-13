<?php
namespace JWeiland\RlmpTmplselector\Tca;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class/Function which manipulates the item-array for table/field pages_tx_rlmptmplselector_main_tmpl.
 */
class AddFilesToSel
{
    /**
     * @var string
     */
    protected $dir = 'templatePathMain';
    
    /**
     * @var string
     */
    protected $branch = 'main.';
    
    /**
     * @var string
     */
    protected $getParams = '';
    
    /**
     * @var array
     */
    protected $getParams_arrayKeys = array();

    /**
     * Manipulating the input array, $params, adding new selectorbox items.
     *
     * @param array $params
     * @param object $parentObject
     *
     * @return void The result is in the manipulated $params array
     */
    public function main(&$params, $parentObject)
    {
        $thePageId = $params['row']['uid'];
        if (!is_numeric($thePageId)) {
            $this->getParams = GeneralUtility::_GET('edit');
            $this->getParams_arrayKeys = array_keys($this->getParams['pages']);
            $thePageId = $this->getParams_arrayKeys[0];
        }

        /** @var \TYPO3\CMS\Core\TypoScript\ExtendedTemplateService $template */
        $template = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\TypoScript\\ExtendedTemplateService'); // Defined global here!
        // Do not log time-performance information
        $template->tt_track = 0;
        $template->init();
        $rootLine = BackendUtility::BEgetRootLine($thePageId);
        // This generates the constants/config + hierarchy info for the template.
        $template->runThroughTemplates($rootLine, 0);
        $template->generateConfig();

        $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['rlmp_tmplselector']);

        // Use external HTML template files:
        if ($extConf['templateMode'] === 'file') {
            // Finding value for the path containing the template files
            $readPath = GeneralUtility::getFileAbsFileName($template->setup['tt_content.']['list.']['20.']['rlmptmplselector_templateselector.']['settings.'][$this->dir]);
            // If that direcotry is valid, is a directory then select files in it:
            if (@is_dir($readPath)) {
                //getting all HTML files in the directory:
                $template_files = GeneralUtility::getFilesInDir($readPath, 'html,htm', 1, 1);

                /** @var \TYPO3\CMS\Core\Html\HtmlParser $parseHTML */
                $parseHTML = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Html\\HtmlParser');

                // Traverse that array:
                foreach ($template_files as $htmlFilePath) {
                    // Reset vars:
                    $selectorBoxItem_icon='';

                    // Reading the content of the template document ...
                    $content = GeneralUtility::getUrl($htmlFilePath);
                    // ... and extracting the content of the title-tags:
                    $parts = $parseHTML->splitIntoBlock('title', $content);
                    $titleTagContent = $parseHTML->removeFirstAndLastTag($parts[1]);
                    // Setting the item label:
                    $selectorBoxItem_title = trim($titleTagContent . ' (' . basename($htmlFilePath) . ')');

                    // Trying to look up an image icon for the template
                    $fileParts = GeneralUtility::split_fileref($htmlFilePath);
                    $testImageFilename = $readPath . $fileParts['filebody'] . '.gif';
                    if (@is_file($testImageFilename)) {
                        $selectorBoxItem_icon = '../' . substr($testImageFilename, strlen(PATH_site));
                    }

                     // Finally add the new item:
                    $params['items'][] = array(
                        $selectorBoxItem_title,
                        basename($htmlFilePath),
                        $selectorBoxItem_icon
                    );
                }
            }
        }

        // Don't use external files - do it the TS way instead
        if ($extConf['templateMode'] === 'ts') {
            // Finding value for the path containing the template files
            $readPath = GeneralUtility::getFileAbsFileName('uploads/tf/');
            $tmplObjects = $template->setup['tt_content.']['list.']['20.']['rlmptmplselector_templateselector.']['settings.']['templateObjects.'][$this->branch];
            // Traverse template objects
            if (is_array($tmplObjects)) {
                reset($tmplObjects);
                while ($tmplObject = each($tmplObjects)) {
                    $k = $tmplObject['key'];
                    $v = $tmplObject['value'];
                    if ($v === 'TEMPLATE') {
                        if (is_array($tmplObjects[$k . '.']['tx_rlmptmplselector.'])) {
                            $selectorBoxItem_title=$tmplObjects[$k . '.']['tx_rlmptmplselector.']['title'];
                            $selectorBoxItem_icon = '';

                            $fileParts = GeneralUtility::split_fileref(trim($tmplObjects[$k . '.']['tx_rlmptmplselector.']['imagefile']));
                            $testImageFilename=$readPath . $fileParts['filebody'] . '.gif';
                            if (@is_file($testImageFilename)) {
                                $selectorBoxItem_icon = '../' . substr($testImageFilename, strlen(PATH_site));
                            }

                            $params['items'][] = array(
                                $selectorBoxItem_title,
                                $k,
                                $selectorBoxItem_icon
                            );
                        }
                    }
                }
            }
        }
    }
}

/**
 * Class AddFilesToSelCa
 *
 * @package JWeiland\RlmpTmplselector\Tca
 */
class AddFilesToSelCa extends AddFilesToSel
{
    /**
     * @var string
     */
    protected $dir = 'templatePathSub';
    
    /**
     * @var string
     */
    protected $branch = 'sub.';
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/rlmp_tmplselector/class.tx_rlmptmplselector_addfilestosel.php']) {
    include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/rlmp_tmplselector/class.tx_rlmptmplselector_addfilestosel.php']);
}
