<?php

declare(strict_types=1);

namespace FriendsOfTYPO3\HeadlessPowermail\ViewHelpers\Form;

use In2code\Powermail\ViewHelpers\Form\SelectFieldViewHelper as Powermail_SelectFieldViewHelper;

class SelectFieldViewHelper extends Powermail_SelectFieldViewHelper
{
    /**
     * Render the tag.
     *
     * @return string rendered tag.
     * @api
     */
    public function render()
    {
        $this->originalOptions = $this->arguments['options'];
        $this->setOptions();
        $options = $this->getOptions();
        return $this->renderOptionTags($options);
    }

    protected function renderOptionTags(array $options): string
    {
        $optionTags = [];
        foreach ($options as $value => $label) {
            $isSelected = $this->isSelectedAlternative($this->getOptionFromOriginalOptionsByValue((string)$value));
            $optionTags[] = json_decode($this->renderOptionTag((string)$value, (string)$label, $isSelected),true);
        }
        return json_encode($optionTags);
    }

    /**
     * Render one option tag
     *
     * @param string $value value attribute of the option tag (will be escaped)
     * @param string $label content of the option tag (will be escaped)
     * @param bool $isSelected specifies wether or not to add selected attribute
     * @return string the rendered option tag
     */
    protected function renderOptionTag($value, $label, $isSelected = false): string
    {
        return json_encode([
            'value' => $value,
            'label' => $label,
            'isSelected' => $isSelected,
        ]);
    }
}
