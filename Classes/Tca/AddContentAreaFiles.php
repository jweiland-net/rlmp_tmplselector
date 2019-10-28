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

/**
 * Class which manipulates the item-array for column tx_rlmptmplselector_ca_tmpl in pages table.
 */
class AddContentAreaFiles extends AbstractAddFiles
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
