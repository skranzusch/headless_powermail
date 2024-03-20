<?php

declare(strict_types=1);

namespace FriendsOfTYPO3\HeadlessPowermail\ViewHelpers\Form;

use In2code\Powermail\ViewHelpers\Form\MultiUploadViewHelper as Powermail_MultiUploadViewHelper;

class MultiUploadViewHelper extends Powermail_MultiUploadViewHelper
{
    public function render(): string
    {
        $name = $this->getName();
        $allowedFields = ['name', 'type', 'tmp_name', 'error', 'size'];
        foreach ($allowedFields as $fieldName) {
            $this->registerFieldNameForFormTokenGeneration($name . '[' . $fieldName . '][]');
        }
        $this->tag->addAttribute('type', 'file');

        if (isset($this->arguments['multiple'])) {
            $this->tag->addAttribute('name', $name . '[]');
        } else {
            $this->tag->addAttribute('name', $name);
        }

        $this->setErrorClassAttribute();
        return $this->renderInput();
    }

    /**
     * Renders and returns the tag
     *
     * @return string
     * @api
     */
    public function renderInput(): string
    {
        $data = [];
        if (empty($this->tagName)) {
            return '';
        }
        foreach ($this->tag->getAttributes() as $attributeName => $attributeValue) {
            $data[$attributeName] = $attributeValue;
        }
        $data['name'] = $this->tag->getTagName();
        if ($this->tag->hasContent()) {
            $data['content'] = $this->tag->getContent();
        }
        return json_encode($data);
    }
}
