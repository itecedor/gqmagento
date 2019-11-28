<?php

namespace POSIMWebExt\GCLink\Plugin;

class FinalPricePlugin
{
    public function beforeSetTemplate(\Magento\Catalog\Pricing\Render\FinalPriceBox $subject, $template)
    {
        if ($subject->getSaleableItem()->getTypeId() == 'posimgc' && $subject->getSaleableItem()->getPrice() == 0) {
            if ($template == 'Magento_Catalog::product/price/final_price.phtml') {
                return ['POSIMWebExt_GCLink::product/price/final_price.phtml'];
            } else {
                return [$template];
            }
        } else {
            return [$template];
        }
    }
}