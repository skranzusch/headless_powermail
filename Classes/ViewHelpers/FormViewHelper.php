<?php

namespace FriendsOfTYPO3\HeadlessPowermail\ViewHelpers;

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

use \TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Extbase\Security\HashScope;

/**
 * Form ViewHelper. Generates a :html:`<form>` Tag.
 *
 * Basic usage
 * ===========
 *
 * Use :html:`<f:form>` to output an HTML :html:`<form>` tag which is targeted
 * at the specified action, in the current controller and package.
 * It will submit the form data via a POST request. If you want to change this,
 * use :html:`method="get"` as an argument.
 *
 * Examples
 * ========
 *
 * A complex form with a specified encoding type
 * ---------------------------------------------
 *
 * Form with enctype set::
 *
 *    <f:form action=".." controller="..." package="..." enctype="multipart/form-data">...</f:form>
 *
 * A Form which should render a domain object
 * ------------------------------------------
 *
 * Binding a domain object to a form::
 *
 *    <f:form action="..." name="customer" object="{customer}">
 *       <f:form.hidden property="id" />
 *       <f:form.textbox property="name" />
 *    </f:form>
 *
 * This automatically inserts the value of ``{customer.name}`` inside the
 * textbox and adjusts the name of the textbox accordingly.
 */
class FormViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var int
     */
    protected $i = 0;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('formUid', 'int', 'Form uid', true);
    }

    public function render(): string
    {
        $this->setFormActionUri();

        if (isset($this->arguments['method']) && strtolower($this->arguments['method']) === 'get') {
            $this->tag->addAttribute('method', 'get');
        } else {
            $this->tag->addAttribute('method', 'post');
        }

        if (isset($this->arguments['novalidate']) && $this->arguments['novalidate'] === true) {
            $this->tag->addAttribute('novalidate', 'novalidate');
        }

        $this->addFormObjectNameToViewHelperVariableContainer();
        $this->addFormObjectToViewHelperVariableContainer();
        $this->addFieldNamePrefixToViewHelperVariableContainer();
        $this->addFormFieldNamesToViewHelperVariableContainer();
        $this->data['pages'] = $this->renderChildren();

        $this->renderHiddenIdentityField($this->arguments['object'] ?? null, $this->getFormObjectName());
        $this->renderAdditionalIdentityFields();
        $this->renderHiddenReferrerFields();

        // Render the trusted list of all properties after everything else has been rendered
        $this->renderFormUidField();
        $this->renderTrustedPropertiesField();

        $this->removeFieldNamePrefixFromViewHelperVariableContainer();
        $this->removeFormObjectFromViewHelperVariableContainer();
        $this->removeFormObjectNameFromViewHelperVariableContainer();
        $this->removeFormFieldNamesFromViewHelperVariableContainer();
        $this->removeCheckboxFieldNamesFromViewHelperVariableContainer();

        return json_encode($this->data);
    }

    /**
     * Sets the "action" attribute of the form tag
     */
    protected function setFormActionUri(): void
    {
        if ($this->hasArgument('actionUri')) {
            $formActionUri = $this->arguments['actionUri'];
        } else {
            if (isset($this->arguments['noCacheHash'])) {
                trigger_error(
                    'Using the argument "noCacheHash" in <f:form> ViewHelper has no effect anymore. Remove the argument in your fluid template, as it will result in a fatal error.',
                    E_USER_DEPRECATED
                );
            }

            $request = $this->getRequest();
            /** @var UriBuilder $uriBuilder */
            $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
            $uriBuilder
                ->reset()
                ->setRequest($request)
                ->setTargetPageType($this->arguments['pageType'] ?? 0)
                ->setNoCache($this->arguments['noCache'] ?? false)
                ->setSection($this->arguments['section'] ?? '')
                ->setCreateAbsoluteUri($this->arguments['absolute'] ?? false)
                ->setArguments(isset($this->arguments['additionalParams']) ? (array)$this->arguments['additionalParams'] : [])
                ->setAddQueryString($this->arguments['addQueryString'] ?? false)
                ->setArgumentsToBeExcludedFromQueryString(isset($this->arguments['argumentsToBeExcludedFromQueryString']) ? (array)$this->arguments['argumentsToBeExcludedFromQueryString'] : [])
                ->setFormat($this->arguments['format'] ?? '');

            $pageUid = (int)($this->arguments['pageUid'] ?? 0);
            if ($pageUid > 0) {
                $uriBuilder->setTargetPageUid($pageUid);
            }

            $formActionUri = $uriBuilder->uriFor(
                $this->arguments['action'] ?? null,
                $this->arguments['arguments'] ?? [],
                $this->arguments['controller'] ?? null,
                $this->arguments['extensionName'] ?? null,
                $this->arguments['pluginName'] ?? null
            );
            $this->formActionUriArguments = $uriBuilder->getArguments();
        }
        $this->data['action'] = $formActionUri;
    }

    /**
     * Render additional identity fields which were registered by form elements.
     * This happens if a form field is defined like property="bla.blubb" - then we might need an identity property for the sub-object "bla".
     *
     * @return string HTML-string for the additional identity properties
     */
    protected function renderAdditionalIdentityFields(): string
    {
        if ($this->viewHelperVariableContainer->exists(\TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper::class, 'additionalIdentityProperties')) {
            $additionalIdentityProperties = $this->viewHelperVariableContainer->get(\TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper::class, 'additionalIdentityProperties');

            foreach ($additionalIdentityProperties as $identity) {
                $this->addHiddenField('identity', $identity);
            }
        }

        return '';
    }

    /**
     * Renders hidden form fields for referrer information about
     * the current controller and action.
     *
     * @return string Hidden fields with referrer information
     * @todo filter out referrer information that is equal to the target (e.g. same packageKey)
     */
    protected function renderHiddenReferrerFields(): string
    {
        $request = $this->getRequest();
        $extensionName = $request->getControllerExtensionName();
        $controllerName = $request->getControllerName();
        $actionName = $request->getControllerActionName();
        $actionRequest = [
            '@extension' => $extensionName,
            '@controller' => $controllerName,
            '@action' => $actionName,
        ];
        $this->addHiddenField($this->prefixFieldName('__referrer[@extension]'), $extensionName);
        $this->addHiddenField($this->prefixFieldName('__referrer[@controller]'), $controllerName);
        $this->addHiddenField($this->prefixFieldName('__referrer[@action]'), $actionName);
        $this->addHiddenField($this->prefixFieldName('__referrer[arguments]'), $this->hashService->appendHmac(base64_encode(serialize($request->getArguments())),HashScope::ReferringArguments->prefix()));
        $this->addHiddenField($this->prefixFieldName('__referrer[@request]'), $this->hashService->appendHmac(json_encode($actionRequest),HashScope::ReferringArguments->prefix()));

        return '';
    }

    /**
     * Adds the field name prefix to the ViewHelperVariableContainer
     */
    protected function addFieldNamePrefixToViewHelperVariableContainer(): void
    {
        $fieldNamePrefix = $this->getFieldNamePrefix();
        $this->renderingContext->getViewHelperVariableContainer()->add(\TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper::class, 'fieldNamePrefix', $fieldNamePrefix);
    }

    /**
     * Renders a hidden form field containing the technical identity of the given object.
     *
     * @param mixed $object Object to create the identity field for
     * @param string $name Name
     *
     * @return string A hidden field containing the Identity (uid) of the given object
     * @see \TYPO3\CMS\Extbase\Mvc\Controller\Argument::setValue()
     */
    protected function renderHiddenIdentityField(mixed $object, ?string $name): string
    {
        if ($object instanceof LazyLoadingProxy) {
            $object = $object->_loadRealInstance();
        }
        if (!is_object($object)
            || !($object instanceof AbstractDomainObject)
            || ($object->_isNew() && !$object->_isClone())) {
            return '';
        }
        // Intentionally NOT using PersistenceManager::getIdentifierByObject here!!
        // Using that one breaks re-submission of data in forms in case of an error.
        $identifier = $object->getUid();
        if ($identifier === null) {
            return '';
        }
        $name = $this->prefixFieldName($name) . '[__identity]';
        $this->registerFieldNameForFormTokenGeneration($name);

        $this->addHiddenField($name, $identifier);

        return '';
    }

    /**
     * Render the request hash field
     */
    protected function renderTrustedPropertiesField(): string
    {
        $formFieldNames = $this->renderingContext->getViewHelperVariableContainer()->get(\TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper::class, 'formFieldNames');
        $requestHash = $this->mvcPropertyMappingConfigurationService->generateTrustedPropertiesToken($formFieldNames, $this->getFieldNamePrefix());
        $this->addHiddenField($this->prefixFieldName('__trustedProperties'), $requestHash);

        return '';
    }

    /**
     * Render the request form uid field
     *
     * @return string|void
     * @throws \TYPO3\CMS\EXTBASE\Security\Exception\InvalidArgumentForHashGenerationException
     */
    protected function renderFormUidField()
    {
        $fieldName = $this->prefixFieldName('mail[form]');
        $this->registerFieldNameForFormTokenGeneration($fieldName);
        $this->addHiddenField($fieldName, $this->arguments['formUid']);
    }

    /**
     * @param $name
     * @param $value
     */
    protected function addHiddenField($name, $value)
    {
        $this->data['hiddenFields'][$this->i]['name'] = $name;
        $this->data['hiddenFields'][$this->i]['value'] = $value;
        $this->i++;
    }

    protected function getRequest(): RequestInterface
    {
        $renderingContext = $this->renderingContext;
        if (
            method_exists($renderingContext, 'getAttribute') &&
            method_exists($renderingContext, 'hasAttribute') &&
            $renderingContext->hasAttribute(ServerRequestInterface::class)
        ) {
            $request = $renderingContext->getAttribute(ServerRequestInterface::class);
        } else {
            $request = $renderingContext->getRequest();
        }
        return $request;
    }
}
