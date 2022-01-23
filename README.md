# TYPO3 Extension "headless_powermail" - Connects together EXT:headless and EXT:powermail

Headless Powermail allows you to render Powermail forms in JSON.

## Features

- JSON API for Powermail extension
- Powermail standard fields are in JSON
- Some of extra fields are converted to JSON such as: Hidden, Date, File, Content, show HTML, show some text
- Supports EXT:powermailrecatcha as well!

## Installation
Install extension using composer\
``composer require friendsoftypo3/headless_powermail``

Then, you should include extension typoscript template, and use provided constants.

## Development
Development for this extension is happening as part of the TYPO3 PWA initiative, see https://typo3.org/community/teams/typo3-development/initiatives/pwa/
