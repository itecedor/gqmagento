<?php

namespace POSIMWebExt\GCLink\Block\Product\View\Type;

class Giftcard extends \Magento\Catalog\Block\Product\View\Type\Simple
{

    public function getGiftCardType()
    {
        return $this->getProduct()->getAttributeText('posimgc_type');
    }

    public function isVirtualAllowed()
    {
        return $this->_scopeConfig->getValue('gclink/defaults/enable_virtual');
    }

    public function isPhysicalAllowed()
    {
        return $this->_scopeConfig->getValue('gclink/defaults/enable_physical');
    }

    public function getMaxAllowedValue()
    {
        return $this->_scopeConfig->getValue('gclink/defaults/max_value');
    }

    public function getMinAllowedValue()
    {
        return $this->_scopeConfig->getValue('gclink/defaults/min_value');
    }
}