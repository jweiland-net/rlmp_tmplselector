<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "rlmp_tmplselector".
 *
 * Auto generated 26-02-2014 09:47
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Page Template Selector',
    'description' => 'Select different templates for each page or tree branch. Easily works with either external html templates or pure TypoScript templates! Modified version based on the Modern Template Building tutorial.',
    'category' => 'be',
    'version' => '2.2.1',
    'module' => '',
    'state' => 'stable',
    'uploadfolder' => 1,
    'createDirs' => '',
    'modify_tables' => 'pages',
    'clearcacheonload' => 1,
    'lockType' => '',
    'author' => 'Stefan Froemken',
    'author_email' => 'projects@jweiland.net',
    'author_company' => 'jweiland.net',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-9.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
