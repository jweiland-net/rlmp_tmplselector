<?php
namespace JWeiland\RlmpTmplselector\Controller;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Plugin 'Template selector' for the 'rlmp_tmplselector' extension.
 */
class TemplateSelectorController extends ActionController
{
    /**
     * @var ContentObjectRenderer
     */
    protected $contentObject;

    /**
     * Initialize show action
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
        $pageSelect = GeneralUtility::makeInstance(PageRepository::class);

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
                    $templateFile = trim($GLOBALS['TSFE']->page['tx_rlmptmplselector_ca_tmpl']);
                    $relPath = $tmplConf['templatePathSub'];
                    // Setting templateFile reference to the currently selected value - or the default if not set:
                    if (empty($templateFile)) {
                        $templateFile = ($tmplConf['defaultTemplateFileNameSub']);
                    }
                    break;
                case 'main':
                default:
                    $templateFile = trim($GLOBALS['TSFE']->page['tx_rlmptmplselector_main_tmpl']);
                    $relPath = $tmplConf['templatePathMain'];
                    // Setting templateFile reference to the currently selected value - or the default if not set:
                    if (empty($templateFile)) {
                        $templateFile = trim($tmplConf['defaultTemplateFileNameMain']);
                    }
                    break;
            }
            // if a value was found, we dare to continue
            if ($relPath) {
                if (strrpos($relPath, '/') != strlen($relPath) - 1) {
                    $relPath .= '/';
                }
                // get absolute filePath:
                $absFilePath = GeneralUtility::getFileAbsFileName($relPath . $templateFile);
                if ($absFilePath && @is_file($absFilePath)) {
                    return GeneralUtility::getUrl($absFilePath);
                }
            }
        }

        // Don't use external files - do it the TS way instead
        if ($confArray['templateMode'] === 'ts') {
            // Getting the 'type' from the input TypoScript configuration:
            switch ((string)$this->settings['templateType']) {
                case 'sub':
                    $templateObjectNr = trim($GLOBALS['TSFE']->page['tx_rlmptmplselector_ca_tmpl']);
                    if (empty($templateObjectNr)) {
                        $templateObjectNr = trim($tmplConf['defaultTemplateObjectSub']);
                    }
                    break;
                case 'main':
                default:
                    $templateObjectNr = trim($GLOBALS['TSFE']->page['tx_rlmptmplselector_main_tmpl']);
                    if (empty($templateObjectNr)) {
                        $templateObjectNr = trim($tmplConf['defaultTemplateObjectMain']);
                    }
                    break;
            }

            // Parse the template
            $lConf = &$tmplConf['templateObjects.'][(string)$this->settings['templateType'] . '.'][$templateObjectNr . '.'];
            return $this->contentObject->render($this->contentObject->getContentObject('TEMPLATE'), $lConf);
        }
        return '';
    }
}
