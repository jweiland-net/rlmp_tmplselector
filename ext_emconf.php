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

$EM_CONF[$_EXTKEY] = array (
	'title' => 'Page Template Selector',
	'description' => 'Select different templates for each page or tree branch. Easily works with either external html templates or pure TypoScript templates! Modified version based on the Modern Template Building tutorial.',
	'category' => 'be',
	'shy' => 1,
	'version' => '2.0.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
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
	'constraints' => array (
		'depends' => array(
			'extbase' => '6.2.0-6.2.99',
			'fluid' => '6.2.0-6.2.99',
			'typo3' => '6.2.0-6.2.99',
		),
		'conflicts' => array (
		),
		'suggests' => array (
		),
	),
);