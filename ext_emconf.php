<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Headless Powermail',
    'description' => 'This extension provides way to output content from TYPO3 in JSON format.',
    'state' => 'stable',
    'author' => 'Oskar Dydo',
    'author_email' => 'extensions@macopedia.pl',
    'category' => 'fe',
    'version' => '4.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
            'powermail' => '13.0.0-13.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
