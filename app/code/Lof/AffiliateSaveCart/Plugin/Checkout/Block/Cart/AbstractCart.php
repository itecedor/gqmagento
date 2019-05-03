<?php

namespace Lof\AffiliateSaveCart\Plugin\Checkout\Block\Cart;

class AbstractCart
{

    public function afterGetItemRenderer(\Magento\Checkout\Block\Cart\AbstractCart $subject, $result)
    {
        if ($subject->getRequest()->getActionName() == 'print') {
            $result->setTemplate('Lof_AffiliateSaveCart::cart/print/item/default.phtml');
        }
        return $result;
    }
}
