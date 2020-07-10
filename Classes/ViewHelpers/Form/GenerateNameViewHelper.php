<?php
namespace FriendsOfTYPO3\HeadlessPowermail\ViewHelpers\Form;

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

/**
 * ViewHelper which creates a text field :html:`<input type="text">`.
 *
 * Examples
 * ========
 *
 * Example::
 *
 *    <f:form.textfield name="myTextBox" value="default value" />
 *
 * Output::
 *
 *    <input type="text" name="myTextBox" value="default value" />
 */
class GenerateNameViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper
{
    /**
     * Initialize the arguments.
     *
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        $this->registerArgument(
            'property',
            'string',
            'Name of Object Property. If used in conjunction with <f:form object="...">, "name" and "value" properties will be ignored.'
        );
        $this->registerArgument('name', 'string', 'Name of input tag');
    }

    /**
     * @return string|void
     */
    public function render()
    {
        return $this->getName();
    }
}
