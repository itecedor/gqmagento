<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_AffiliateSaveCart
 * @copyright  Copyright (c) 2018 Landofcoder (https://www.landofcoder.com/)
 * @license    https://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\AffiliateSaveCart\Model\Config\Source;
 
class Emailtemplate implements \Magento\Framework\Option\ArrayInterface
{
	protected $_emailConfig;
	protected $_templatesFactory;
	
	public function __construct(
        \Magento\Email\Model\ResourceModel\Template\CollectionFactory $templatesFactory,
        \Magento\Email\Model\Template\Config $emailConfig
    ) {
        $this->_templatesFactory = $templatesFactory;
        $this->_emailConfig = $emailConfig;
    }

    public function toOptionArray()
    {
    	$collection = $this->_templatesFactory->create();
        $collection->load();
        $options = $collection->toOptionArray();

        $templateId = 'affiliatesavecart_email_notify_template';
        $templateLabel = $this->_emailConfig->getTemplateLabel($templateId);
        $templateLabel = __('%1 (Default)', $templateLabel);
        array_unshift($options, ['value' => $templateId, 'label' => $templateLabel]);

        return $options;
    }
}