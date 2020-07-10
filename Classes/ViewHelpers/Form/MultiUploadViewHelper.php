<?php

declare(strict_types=1);

namespace FriendsOfTYPO3\HeadlessPowermail\ViewHelpers\Form;

use TYPO3\CMS\Fluid\ViewHelpers\Form\UploadViewHelper;

/**
 * Class MultiUploadViewHelper
 */
class MultiUploadViewHelper extends UploadViewHelper
{

    /**
     * Initialize the arguments.
     *
     * @api
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
    }

    /**
     * Renders the upload field.
     *
     * @return string
     */
    public function render(): string
    {
        $name = $this->getName();
        $allowedFields = ['name', 'type', 'tmp_name', 'error', 'size'];
        foreach ($allowedFields as $fieldName) {
            $this->registerFieldNameForFormTokenGeneration($name . '[' . $fieldName . '][]');
        }
        $this->tag->addAttribute('type', 'file');
        $name .= '[]';
        $this->tag->addAttribute('name', $name);
        $this->setErrorClassAttribute();
        return $this->renderInput();
    }

    /**
     * Renders and returns the tag
     *
     * @return string
     * @api
     */
    public function renderInput()
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
