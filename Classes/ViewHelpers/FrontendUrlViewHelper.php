<?php

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

declare(strict_types=1);

namespace FriendsOfTYPO3\HeadlessPowermail\ViewHelpers;

use FriendsOfTYPO3\Headless\Utility\UrlUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FrontendUrlViewHelper extends AbstractViewHelper
{
    /**
     * Initialize
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('url', 'string', 'url', false);
        $this->registerArgument('pid', 'int', 'pid', true);
    }

    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $url = $arguments['url'] ?? $renderChildrenClosure();
        $pid = $arguments['pid'];

        $urlUtility = GeneralUtility::makeInstance(UrlUtility::class);

        $url = $urlUtility->getFrontendUrlForPage($url, $pid);

        return $url;
    }
}
