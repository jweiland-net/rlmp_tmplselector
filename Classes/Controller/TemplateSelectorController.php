<?php
declare(strict_types = 1);
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

use JWeiland\RlmpTmplselector\Configuration\ExtConf;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
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
     * @return string
     */
    public function showAction(): string
    {
        // GETTING configuration for the extension:
        $extConf = GeneralUtility::makeInstance(ExtConf::class);
        $settings = $this->getSettings();
        $pageSelect = GeneralUtility::makeInstance(PageRepository::class);

        // If we should inherit the template from above the current page, search for the next selected template
        // and make it the default template
        if (is_array($this->getTypoScriptFrontendController()->rootLine)) {
            if ((int)$settings['inheritMainTemplates'] === 1) {
                foreach ($this->getTypoScriptFrontendController()->rootLine as $rootLinePage) {
                    $page = $pageSelect->getPage($rootLinePage['uid']);
                    if ($page['tx_rlmptmplselector_main_tmpl']) {
                        $settings['defaultTemplateFileNameMain'] = $settings['defaultTemplateObjectMain'] = $page['tx_rlmptmplselector_main_tmpl'];
                        break;
                    }
                }
            }
            if ((int)$settings['inheritSubTemplates'] === 1) {
                foreach ($this->getTypoScriptFrontendController()->rootLine as $rootLinePage) {
                    $page = $pageSelect->getPage($rootLinePage['uid']);
                    if ($page['tx_rlmptmplselector_ca_tmpl']) {
                        $settings['defaultTemplateFileNameSub'] = $settings['defaultTemplateObjectSub'] = $page['tx_rlmptmplselector_ca_tmpl'];
                        break;
                    }
                }
            }
        }

        // Determine mode: If it is 'file', work with external HTML template files
        if ($extConf->getTemplateMode() === 'file') {
            // Getting the 'type' from the input TypoScript configuration:
            switch ((string)$this->settings['templateType']) {
                case 'sub':
                    $templateFile = trim($this->getTypoScriptFrontendController()->page['tx_rlmptmplselector_ca_tmpl']);
                    $relPath = $settings['templatePathSub'];
                    // Setting templateFile reference to the currently selected value - or the default if not set:
                    if (empty($templateFile)) {
                        $templateFile = ($settings['defaultTemplateFileNameSub']);
                    }
                    break;
                case 'main':
                default:
                    $templateFile = trim($this->getTypoScriptFrontendController()->page['tx_rlmptmplselector_main_tmpl']);
                    $relPath = $settings['templatePathMain'];
                    // Setting templateFile reference to the currently selected value - or the default if not set:
                    if (empty($templateFile)) {
                        $templateFile = trim($settings['defaultTemplateFileNameMain']);
                    }
                    break;
            }
            // if a value was found, we dare to continue
            if ($relPath) {
                $relPath = rtrim($relPath, '/') . '/';
                $absFilePath = GeneralUtility::getFileAbsFileName($relPath . $templateFile);
                if ($absFilePath && @is_file($absFilePath)) {
                    return GeneralUtility::getUrl($absFilePath);
                }
            }
        }

        // Don't use external files - do it the TS way instead
        if ($extConf->getTemplateMode() === 'ts') {
            // Getting the 'type' from the input TypoScript configuration:
            switch ((string)$this->settings['templateType']) {
                case 'sub':
                    $templateObjectNr = trim($this->getTypoScriptFrontendController()->page['tx_rlmptmplselector_ca_tmpl']);
                    if (empty($templateObjectNr)) {
                        $templateObjectNr = trim($settings['defaultTemplateObjectSub']);
                    }
                    break;
                case 'main':
                default:
                    $templateObjectNr = trim($this->getTypoScriptFrontendController()->page['tx_rlmptmplselector_main_tmpl']);
                    if (empty($templateObjectNr)) {
                        $templateObjectNr = trim($settings['defaultTemplateObjectMain']);
                    }
                    break;
            }

            // Parse the template
            return $this->contentObject->render(
                $this->contentObject->getContentObject('TEMPLATE'),
                $settings['templateObjects.'][(string)$this->settings['templateType'] . '.'][$templateObjectNr . '.']
            );
        }
        return '';
    }

    protected function getSettings(): array
    {
        return ArrayUtility::getValueByPath(
            $this->getTypoScriptFrontendController()->tmpl->setup,
            'tt_content./list./20./rlmptmplselector_templateselector./settings.'
        );
    }

    protected function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
