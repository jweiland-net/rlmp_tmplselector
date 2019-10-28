<?php
namespace JWeiland\RlmpTmplselector\Tca;

/*
 * This file is part of the rlmp_tmplselector project.
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

use JWeiland\RlmpTmplselector\Configuration\ExtConf;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Html\HtmlParser;
use TYPO3\CMS\Core\TypoScript\ExtendedTemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Abstract class to add template files to various selectors in pages table.
 */
abstract class AbstractAddFiles
{
    /**
     * Override with "templatePathMain" or "templatePathSub"
     *
     * @var string
     */
    protected $dir = '';

    /**
     * Override with "main." or "sub."
     *
     * @var string
     */
    protected $branch = '';

    /**
     * @var string
     */
    protected $getParams = '';

    /**
     * @var array
     */
    protected $getParams_arrayKeys = [];

    /**
     * Manipulating the input array, $params, adding new selectorbox items.
     *
     * @param array $params
     * @param object $parentObject
     */
    public function main(&$params, $parentObject)
    {
        $thePageId = $params['row']['uid'];
        if (!is_numeric($thePageId)) {
            $this->getParams = GeneralUtility::_GET('edit');
            $this->getParams_arrayKeys = array_keys($this->getParams['pages']);
            $thePageId = $this->getParams_arrayKeys[0];
        }

        $template = GeneralUtility::makeInstance(ExtendedTemplateService::class); // Defined global here!
        // Do not log time-performance information
        $template->tt_track = 0;
        $template->init();
        $rootLine = BackendUtility::BEgetRootLine($thePageId);
        // This generates the constants/config + hierarchy info for the template.
        $template->runThroughTemplates($rootLine, 0);
        $template->generateConfig();

        $extConf = GeneralUtility::makeInstance(ExtConf::class);

        // Use external HTML template files:
        if ($extConf->getTemplateMode() === 'file') {
            // Finding value for the path containing the template files
            $readPath = GeneralUtility::getFileAbsFileName(
                $template->setup['tt_content.']['list.']['20.']['rlmptmplselector_templateselector.']['settings.'][$this->dir]
            );
            // If that directory is valid, is a directory then select files in it:
            if (@is_dir($readPath)) {
                //getting all HTML files in the directory:
                $template_files = GeneralUtility::getFilesInDir($readPath, 'html,htm', 1, 1);

                $parseHTML = GeneralUtility::makeInstance(HtmlParser::class);

                // Traverse that array:
                foreach ($template_files as $htmlFilePath) {
                    // Reset vars:
                    $selectorBoxItem_icon = '';

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
                    $params['items'][] = [
                        $selectorBoxItem_title,
                        basename($htmlFilePath),
                        $selectorBoxItem_icon
                    ];
                }
            }
        }

        // Don't use external files - do it the TS way instead
        if ($extConf->getTemplateMode() === 'ts') {
            // Finding value for the path containing the template files
            $readPath = GeneralUtility::getFileAbsFileName('uploads/tf/');
            $tmplObjects = $template->setup['tt_content.']['list.']['20.']['rlmptmplselector_templateselector.']['settings.']['templateObjects.'][$this->branch];
            // Traverse template objects
            if (is_array($tmplObjects)) {
                reset($tmplObjects);
                foreach ($tmplObjects as $tmplObject) {
                    $k = $tmplObject['key'];
                    $v = $tmplObject['value'];
                    if ($v === 'TEMPLATE') {
                        if (is_array($tmplObjects[$k . '.']['tx_rlmptmplselector.'])) {
                            $selectorBoxItem_title = $tmplObjects[$k . '.']['tx_rlmptmplselector.']['title'];
                            $selectorBoxItem_icon = '';

                            $fileParts = GeneralUtility::split_fileref(trim($tmplObjects[$k . '.']['tx_rlmptmplselector.']['imagefile']));
                            $testImageFilename=$readPath . $fileParts['filebody'] . '.gif';
                            if (@is_file($testImageFilename)) {
                                $selectorBoxItem_icon = '../' . substr($testImageFilename, strlen(PATH_site));
                            }

                            $params['items'][] = [
                                $selectorBoxItem_title,
                                $k,
                                $selectorBoxItem_icon
                            ];
                        }
                    }
                }
            }
        }
    }
}
