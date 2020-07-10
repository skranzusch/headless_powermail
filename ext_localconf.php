<?php
defined('TYPO3_MODE') || die();

call_user_func(
    function ($extensionKey) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['headlesspowermail'] = [
            'FriendsOfTYPO3\HeadlessPowermail\ViewHelpers'
        ];

        $GLOBALS['TYPO3_CONF_VARS']['FE']['contentRenderingTemplates'][] = 'headless_powermail/Configuration/TypoScript/';
    },
    'headless_powermail'
);
