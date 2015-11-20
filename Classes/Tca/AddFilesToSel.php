<?php
namespace JWeiland\RlmpTmplselector\Tca;

/***************************************************************
*  Copyright notice
*
*  (c) 2014 Stefan Froemken (projects@jweiland.net)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class/Function which manipulates the item-array for table/field pages_tx_rlmptmplselector_main_tmpl.
 */
class AddFilesToSel
{

    protected $dir = 'templatePathMain';
    protected $branch = 'main.';
    protected $getParams = '';
    protected $getParams_arrayKeys = array();

    /**
     * Manipulating the input array, $params, adding new selectorbox items.
     *
     * @param array $params: Parameters of the user function caller
     * @param object $pObj: Reference to the parent object calling this function
     * @return void The result is in the manipulated $params array
     */
    public function main(&$params, &$pObj)
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

        // GETTING configuration for the extension:
        $confarray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['rlmp_tmplselector']);

        // Use external HTML template files:
        if ($confarray['templateMode'] === 'file') {
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
                    $fI = GeneralUtility::split_fileref($htmlFilePath);
                    $testImageFilename = $readPath . $fI['filebody'] . '.gif';
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
        if ($confarray['templateMode'] === 'ts') {
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
                            unset($selectorBoxItem_icon);

                            $fI = GeneralUtility::split_fileref(trim($tmplObjects[$k . '.']['tx_rlmptmplselector.']['imagefile']));
                            $testImageFilename=$readPath . $fI['filebody'] . '.gif';
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

class AddFilesToSelCa extends AddFilesToSel
{
    protected $dir = 'templatePathSub';
    protected $branch = 'sub.';
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/rlmp_tmplselector/class.tx_rlmptmplselector_addfilestosel.php']) {
    include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/rlmp_tmplselector/class.tx_rlmptmplselector_addfilestosel.php']);
}
