<?php
namespace JWeiland\RlmpTmplselector\Controller;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Plugin 'Template selector' for the 'rlmp_tmplselector' extension.
 */
class TemplateSelectorController extends ActionController
{
    /**
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    protected $contentObject;

    /**
     * initialize show action
     *
     * @return void
     */
    public function initializeShowAction()
    {
        $this->contentObject = $this->configurationManager->getContentObject();
    }

    /**
     * Reads the template-html file which is pointed to by the selector box on the page
     * and type parameter send through TypoScript.
     *
     * @return string The HTML template
     */
    public function showAction()
    {
        // GETTING configuration for the extension:
        $confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['rlmp_tmplselector']);
        $tmplConf = $GLOBALS['TSFE']->tmpl->setup['tt_content.']['list.']['20.']['rlmptmplselector_templateselector.']['settings.'];
        $rootLine = $GLOBALS['TSFE']->rootLine;
        /** @var \TYPO3\CMS\Frontend\Page\PageRepository $pageSelect */
        $pageSelect = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Page\\PageRepository');

        // If we should inherit the template from above the current page, search for the next selected template
        // and make it the default template
        if (is_array($rootLine)) {
            if ((int)$tmplConf['inheritMainTemplates'] === 1) {
                foreach ($rootLine as $rootLinePage) {
                    $page = $pageSelect->getPage($rootLinePage['uid']);
                    if ($page['tx_rlmptmplselector_main_tmpl']) {
                        $tmplConf['defaultTemplateFileNameMain'] = $tmplConf['defaultTemplateObjectMain'] = $page['tx_rlmptmplselector_main_tmpl'];
                        break;
                    }
                }
            }
            if ((int)$tmplConf['inheritSubTemplates'] === 1) {
                foreach ($rootLine as $rootLinePage) {
                    $page = $pageSelect->getPage($rootLinePage['uid']);
                    if ($page['tx_rlmptmplselector_ca_tmpl']) {
                        $tmplConf['defaultTemplateFileNameSub'] = $tmplConf['defaultTemplateObjectSub'] = $page['tx_rlmptmplselector_ca_tmpl'];
                        break;
                    }
                }
            }
        }

        // Determine mode: If it is 'file', work with external HTML template files
        if ($confArray['templateMode'] === 'file') {
            // Getting the 'type' from the input TypoScript configuration:
            switch ((string)$this->settings['templateType']) {
                case 'sub':
                    $templateFile = $GLOBALS['TSFE']->page['tx_rlmptmplselector_ca_tmpl'];
                    $relPath = $tmplConf['templatePathSub'];
                    // Setting templateFile reference to the currently selected value - or the default if not set:
                    if (! $templateFile) {
                        $templateFile = $tmplConf['defaultTemplateFileNameSub'];
                    }
                    break;
                case 'main':
                default:
                    $templateFile = $GLOBALS['TSFE']->page['tx_rlmptmplselector_main_tmpl'];
                    $relPath = $tmplConf['templatePathMain'];
                    // Setting templateFile reference to the currently selected value - or the default if not set:
                    if (! $templateFile) {
                        $templateFile = $tmplConf['defaultTemplateFileNameMain'];
                    }
                    break;
            }
            // if a value was found, we dare to continue
            if ($relPath) {
                if (strrpos($relPath, '/') != strlen($relPath) - 1) {
                    $relPath .= '/';
                }
                // get absolute filePath:
                $absFilePath = GeneralUtility::getFileAbsFileName($relPath.$templateFile);
                if ($absFilePath && @is_file($absFilePath)) {
                    $content = GeneralUtility::getUrl($absFilePath);
                    return $content;
                }
            }
        }

        // Don't use external files - do it the TS way instead
        if ($confArray['templateMode'] === 'ts') {
            // Getting the 'type' from the input TypoScript configuration:
            switch ((string)$this->settings['templateType']) {
                case 'sub':
                    $templateObjectNr = $GLOBALS['TSFE']->page['tx_rlmptmplselector_ca_tmpl'];
                    if (!$templateObjectNr) {
                        $templateObjectNr = $tmplConf['defaultTemplateObjectSub'];
                    }
                    break;
                case 'main':
                default:
                    $templateObjectNr = $GLOBALS['TSFE']->page['tx_rlmptmplselector_main_tmpl'];
                    if (!$templateObjectNr) {
                        $templateObjectNr = $tmplConf['defaultTemplateObjectMain'];
                    }
                    break;
            }

            // Parse the template
            $lConf = &$tmplConf['templateObjects.'][(string)$this->settings['templateType'] . '.'][$templateObjectNr . '.'];
            $content = $this->contentObject->TEMPLATE($lConf);
            return $content;
        }
        return '';
    }
}
