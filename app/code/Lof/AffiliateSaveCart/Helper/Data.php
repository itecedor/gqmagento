<?php

namespace Lof\AffiliateSaveCart\Helper;

use Magento\Framework\App\Helper\Context;
use Lof\AffiliateSaveCart\Api\AffiliateSaveCartRepositoryInterface;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var AffiliateSaveCartRepositoryInterface
     */
    private $saveCartRepository;

    /**
     *@var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        AffiliateSaveCartRepositoryInterface $saveCartRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Context $context
    ) {
        parent::__construct($context);
        $this->saveCartRepository = $saveCartRepository;
        $this->_storeManager = $storeManager;
    }


    public function getQuoteNumber($quote)
    {
        if(is_numeric($quote)) {
            return 'Q' . str_pad($quote, 9, '0', STR_PAD_LEFT);
        } else {
            return 'Q' . str_pad($quote->getId(), 9, '0', STR_PAD_LEFT);
        }
        
    } 

    public function getRealQuoteId($quote_number){
        $quote_id = str_replace("Q","",$quote_number);
        $quote_id = ltrim($quote_id, '0');
        return (int)$quote_id;
    }

    /**
     * @param int $cartId
     * @return bool|\Lof\AffiliateSaveCart\Api\Data\AffiliateSaveCartInterface
     */
    public function isQuoteSaved($cartId)
    {
        return $this->saveCartRepository->isQuoteSaved($cartId);
    }

    public function getConfig($key, $store = null)
    {
        $store = $this->_storeManager->getStore($store);

        $result = $this->scopeConfig->getValue(
            'lofaffiliate/'.$key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }
}
