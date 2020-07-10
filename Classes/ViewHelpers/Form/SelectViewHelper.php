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
 * Returns options in array with value, name
 */
class SelectViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Form\SelectViewHelper
{
    /**
     * @var array
     */
    public $data = [];

    /**
     * @var int
     */
    public $elementCounter = 0;

    /**
     * Render the tag.
     *
     * @return string rendered tag.
     */
    public function render()
    {
        if (isset($this->arguments['required']) && $this->arguments['required']) {
            $this->tag->addAttribute('required', 'required');
        }
        $name = $this->getName();
        if (isset($this->arguments['multiple']) && $this->arguments['multiple']) {
            $this->tag->addAttribute('multiple', 'multiple');
            $name .= '[]';
        }
        $this->tag->addAttribute('name', $name);
        $options = $this->getOptions();

        $this->addAdditionalIdentityPropertiesIfNeeded();
        $this->setErrorClassAttribute();
        $content = '';
        // register field name for token generation.
        // in case it is a multi-select, we need to register the field name
        // as often as there are elements in the box
        if (isset($this->arguments['multiple']) && $this->arguments['multiple']) {
            $content .= $this->renderHiddenFieldForEmptyValue();
            $optionsCount = count($options);
            for ($i = 0; $i < $optionsCount; $i++) {
                $this->registerFieldNameForFormTokenGeneration($name);
            }
            // save the parent field name so that any child f:form.select.option
            // tag will know to call registerFieldNameForFormTokenGeneration
            // this is the reason why "self::class" is used instead of static::class (no LSB)
            $this->viewHelperVariableContainer->addOrUpdate(
                self::class,
                'registerFieldNameForFormTokenGeneration',
                $name
            );
        } else {
            $this->registerFieldNameForFormTokenGeneration($name);
        }

        $this->viewHelperVariableContainer->addOrUpdate(self::class, 'selectedValue', $this->getSelectedValue());
        $this->renderPrependOptionTag();
        $this->renderOptionTags($options);

        //TODO check if we need it in 'production mode'
        $childContent = $this->renderChildren();
        $this->viewHelperVariableContainer->remove(self::class, 'selectedValue');
        $this->viewHelperVariableContainer->remove(self::class, 'registerFieldNameForFormTokenGeneration');

        return $this->data;
    }

    /**
     * Render the option tags.
     *
     * @param array $options the options for the form.
     */
    protected function renderOptionTags($options)
    {
        foreach ($options as $value => $label) {
            $isSelected = $this->isSelected($value);
            $this->renderOptionTag($value, $label, $isSelected) . LF;
        }
    }

    /**
     * Render one option tag
     *
     * @param string $value value attribute of the option tag (will be escaped)
     * @param string $label content of the option tag (will be escaped)
     * @param bool $isSelected specifies whether or not to add selected attribute
     * @return string the rendered option tag
     */
    protected function renderOptionTag($value, $label, $isSelected)
    {
        $this->data[$this->elementCounter]['value'] = $value;
        $this->data[$this->elementCounter]['label'] = $label;
        $this->data[$this->elementCounter]['isSelected'] = $isSelected;

        $this->elementCounter++;
    }
}
