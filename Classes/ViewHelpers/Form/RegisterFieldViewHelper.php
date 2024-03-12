<?php
namespace FriendsOfTYPO3\HeadlessPowermail\ViewHelpers\Form;

use TYPO3\CMS\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
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
 * Registers field for generating hidden fields
 */
class RegisterFieldViewHelper extends AbstractFormFieldViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('name', 'string', 'Name of input tag');
        $this->registerArgument('value', 'mixed', 'Value of input tag');
        $this->registerArgument(
            'property',
            'string',
            'Name of Object Property. If used in conjunction with <f:form object="...">, "name" and "value" properties will be ignored.'
        );
        $this->registerArgument('additionalAttributes', 'array', 'Additional tag attributes. They will be added directly to the resulting HTML tag.', false);
        $this->registerArgument('checked', 'bool', 'Specifies that the input element should be preselected');
        $this->registerArgument('multiple', 'bool', 'Specifies whether this checkbox belongs to a multivalue (is part of a checkbox group)', false, false);
    }

    public function render(): string
    {
        $nameAttribute = $this->getName();

        $checked = $this->arguments['checked'];
        $multiple = $this->arguments['multiple'];

        $propertyValue = null;
        if ($this->hasMappingErrorOccurred()) {
            $propertyValue = $this->getLastSubmittedFormData();
        }
        if ($checked === null && $propertyValue === null) {
            $propertyValue = $this->getPropertyValue();
        }

        if ($propertyValue instanceof \Traversable) {
            $propertyValue = iterator_to_array($propertyValue);
        }
        if (is_array($propertyValue)) {
            $nameAttribute .= '[]';
        } elseif ($multiple === true) {
            $nameAttribute .= '[]';
        }

        $this->registerFieldNameForFormTokenGeneration($nameAttribute);

        return '';
    }
}
