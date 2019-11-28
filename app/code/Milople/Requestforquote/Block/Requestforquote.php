<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/contact-us.html
*
* @category    Ecommerce
* @package     Milople_Requestforquote
* @copyright   Copyright (c) 2017 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url         https://www.milople.com/magento2-extension/request-for-quote-m2.html
*
**/
namespace Milople\Requestforquote\Block;
/**
 * Product price block
 */
use Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\Render\RendererPool;
class Requestforquote extends \Magento\Catalog\Pricing\Render\FinalPriceBox 
{
     /**
     * Wrap with standard required container
     *
     * @param string $html
     * @return string
     */
	public function __construct(
        Context $context,
        SaleableInterface $saleableItem,
        PriceInterface $price,
        RendererPool $rendererPool,
		\Milople\Requestforquote\Helper\Requestforquote $requestforquote_helper,
	    array $data = [],
        SalableResolverInterface $salableResolver = null
    ) {
		$this ->_logger = $context->getLogger();
		$this->helper=$requestforquote_helper;
		$this->scopeConfig=$context->getScopeConfig();
        parent::__construct($context, $saleableItem, $price, $rendererPool, $data);
        $this->salableResolver = $salableResolver ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(SalableResolverInterface::class);
    }
	//get configuration data
    public function wrapResult($html)
    {
			$conf = $this->scopeConfig->getValue('requestforquote/license_status_group/status');
			if((($this->getSaleableItem()->getEnableRequestforquote() and $conf == 'specific_products') || $conf == 'all_products') and $this->helper->isValidCustomer() == true):
				return '<div class="price-box" style="display:none;"><span></span></div>';
			else :
				return
					'<div class="price-box ' . $this->getData('css_classes') . '" ' .
					'data-role="priceBox" ' .
					'data-product-id="' . $this->getSaleableItem()->getId() . '"' .
					'>' . $html . '</div>';
			endif;
    }
}