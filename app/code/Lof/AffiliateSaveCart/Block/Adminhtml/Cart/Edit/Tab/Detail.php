<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://venustheme.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Venustheme
 * @package    Lof_AffiliateSaveCart
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Lof\AffiliateSaveCart\Block\Adminhtml\Cart\Edit\Tab;

class Detail extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Lof\AffiliateSaveCart\Model\ResourceModel\AffiliateSaveCart\Collection
     */
    protected $_saveCartCollection;

    protected $_objectManager;

    /**
     * @param \Magento\Backend\Block\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Magento\Framework\Data\FormFactory
     * @param \Magento\Store\Model\System\Store
     * @param \Magento\Cms\Model\Wysiwyg\Config
     * @param array
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Lof\AffiliateSaveCart\Model\ResourceModel\AffiliateSaveCart\Collection $saveCartCollection,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_objectManager= $objectManager;
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_saveCartCollection = $saveCartCollection;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('affiliate_cart');
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Lof_AffiliateSaveCart::cart_edit') || $this->_isAllowedAction('Lof_AffiliateSaveCart::cart_new')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('savecart');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General')]);

        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }

        $fieldset->addField(
            'quote_name',
            'text',
            [
                'name' => 'quote_name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );

        $accountAffiliate = $this->_objectManager->create('Lof\Affiliate\Model\AccountAffiliate')
        ->loadByAttribute('customer_id', $model->getCustomerId());

        $model->setData('referrer', $accountAffiliate->getFullname());

        $fieldset->addField(
            'referrer',
            'link',
            [
                'name' => 'referrer',
                'label' => __('Referrer'),
                'title' => __('Referrer'),
                'href' => $this->getUrl('affiliate/accountaffiliate/edit', ['accountaffiliate_id' => $accountAffiliate->getId()]),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'commission',
            'text',
            [
                'name' => 'commission',
                'label' => __('Commission'),
                'title' => __('Commission'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );

        $this->_eventManager->dispatch('adminhtml_affiliatesavecart_savecart_edit_tab_detail_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getAccountCollection(){
        $model = $this->_coreRegistry->registry('affiliate_cart');
        $collection = $this->_accountCollection
            ->addFieldToFilter('entity_id', array('neq' => $model->getId()));
        return $collection;
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Cart Affiliate Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Cart Affiliate Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
