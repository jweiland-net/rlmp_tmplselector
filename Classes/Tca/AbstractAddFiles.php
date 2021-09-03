<?php
declare(strict_types = 1);
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
use TYPO3\CMS\Core\Utility\ArrayUtility;
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
    public function main(array &$params, $parentObject)
    {
        $settings = $this->getSettings($this->getPageId($params));
        $extConf = GeneralUtility::makeInstance(ExtConf::class);

        // Use external HTML template files:
        if ($extConf->getTemplateMode() === 'file') {
            // Read template files from configured path
            $readPath = GeneralUtility::getFileAbsFileName($settings[$this->dir]);
            // If that directory is valid, is a directory then select files in it:
            if (@is_dir($readPath)) {
                //getting all HTML files in the directory:
                $templateFiles = GeneralUtility::getFilesInDir($readPath, 'html,htm', 1, 1);

                // Traverse that array:
                foreach ($templateFiles as $templateFilePath) {
                    // Reset vars:
                    $iconForSelectorBox = '';

                    // Trying to look up an image icon for the template
                    $templateFileParts = GeneralUtility::split_fileref($templateFilePath);
                    $testImageFilename = $readPath . $templateFileParts['filebody'] . '.gif';
                    if (@is_file($testImageFilename)) {
                        $iconForSelectorBox = '../' . substr($testImageFilename, strlen(PATH_site));
                    }

                    // Finally add the new item:
                    $params['items'][] = [
                        $this->buildTitleFromTemplateFile($templateFilePath),
                        basename($templateFilePath),
                        $iconForSelectorBox
                    ];
                }
            }
        }

        // Don't use external files - do it the TS way instead
        if ($extConf->getTemplateMode() === 'ts') {
            $contentObjects = $settings['templateObjects.'][$this->branch];
            // Traverse template objects
            if (is_array($contentObjects)) {
                reset($contentObjects);
                foreach ($contentObjects as $key => $contentObject) {
                    $key = (string)$key;
                    // do not process Keys like "10."
                    if (strpos($key, '.') !== false) {
                        continue;
                    }
                    if (in_array($contentObject, ['TEMPLATE', 'FLUIDTEMPLATE'])) {
                        $settings = $this->getTypoScriptSettings($contentObjects, $key);
                        if (!empty($settings)) {
                            $params['items'][] = [
                                $settings['title'] ?? '[no title defined]',
                                $key,
                                $this->getIconPathByTypoScriptSettings($settings)
                            ];
                        }
                    }
                }
            }
        }
    }

    protected function getTypoScriptSettings(array $contentObjects, string $contentObjectKey): array
    {
        try {
            $settings = ArrayUtility::getValueByPath(
                $contentObjects,
                $contentObjectKey . './tx_rlmptmplselector.'
            );
        } catch (\RuntimeException $e) {
            $settings = [];
        }
        return $settings;
    }

    protected function getIconPathByTypoScriptSettings(array $settings): string
    {
        $iconForSelectorBox = '';
        if (
            array_key_exists('imagefile', $settings)
            && !empty($settings['imagefile'])
        ) {
            $iconFilePath = GeneralUtility::getFileAbsFileName(trim($settings['imagefile']));
            $iconFileParts = GeneralUtility::split_fileref($iconFilePath);
            if (
                @is_file($iconFilePath)
                && in_array($iconFileParts['fileext'], ['gif', 'jpg', 'jpeg', 'png', 'svg'], true)
            ) {
                $iconForSelectorBox = $iconFilePath;
            }
        }
        return $iconForSelectorBox;
    }

    protected function buildTitleFromTemplateFile(string $templateFilePath): string
    {
        // Set a title based on filename. Can be prefixed by a <title> tag, if found in template itself
        $title = basename($templateFilePath);

        // Try to get title from content of template
        $htmlParser = GeneralUtility::makeInstance(HtmlParser::class);
        $content = GeneralUtility::getUrl($templateFilePath);
        $titleParts = $htmlParser->splitIntoBlock('title', $content);
        if (!empty($titleParts)) {
            $titleTagContent = trim($htmlParser->removeFirstAndLastTag($titleParts[1] ?: '') ?: '');

            // If a title was found in template, set it as a prefix to filename
            if ($titleTagContent) {
                $title = sprintf('%s (%s)', $titleTagContent, $title);
            }
        }

        return $title;
    }

    protected function getSettings(int $pageId): array
    {
        $templateService = $this->getTemplateServiceForPageId($pageId);
        try {
            $settings = ArrayUtility::getValueByPath(
                $templateService->setup,
                'tt_content./list./20./rlmptmplselector_templateselector./settings.'
            );
        } catch (\RuntimeException $e) {
            $settings = [];
        }
        return $settings;
    }

    protected function getPageId(array $params): int
    {
        $pageId = $params['row']['uid'];
        if (!is_numeric($pageId)) {
            $this->getParams = GeneralUtility::_GET('edit');
            $this->getParams_arrayKeys = array_keys($this->getParams['pages']);
            $pageId = $this->getParams_arrayKeys[0];
        }
        return (int)$pageId;
    }

    protected function getTemplateServiceForPageId(int $pageId): ExtendedTemplateService
    {
        $templateService = GeneralUtility::makeInstance(ExtendedTemplateService::class);

        // Do not log time-performance information
        $templateService->tt_track = 0;
        $templateService->init();

        // This generates the constants/config + hierarchy info for the template.
        $templateService->runThroughTemplates(BackendUtility::BEgetRootLine($pageId), 0);
        $templateService->generateConfig();

        return $templateService;
    }
}
