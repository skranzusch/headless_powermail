<?php
defined('TYPO3') || die();

call_user_func(function () {
    /**
     * Default TypoScript for Headless Powermail
     */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        'headless_powermail',
        'Configuration/TypoScript',
        'Headless Powermail'
    );
});
