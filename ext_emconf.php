<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Headless Powermail',
    'description' => 'This extension provides way to output content from TYPO3 in JSON format.',
    'state' => 'stable',
    'author' => 'Oskar Dydo',
    'author_email' => 'extensions@macopedia.pl',
    'category' => 'fe',
    'version' => '3.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
            'powermail' => '12.0.0-12.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
