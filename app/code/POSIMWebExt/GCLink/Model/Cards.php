<?php

namespace POSIMWebExt\GCLink\Model;

use \POSIMWebExt\GCLink\Api\Data\CardsInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class Cards extends AbstractExtensibleModel implements CardsInterface
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('\POSIMWebExt\GCLink\Model\ResourceModel\Cards');
    }

    /**
     * Get card_id
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::CARD_ID);
    }

    /**
     * Get gift card number
     * @return string|null
     */
    public function getGiftCardNum()
    {
        return $this->getData(self::GC_NUM);
    }

    /**
     * Get gift card balance
     * @return float|null
     */
    public function getBalance()
    {
        return $this->getData(self::BALANCE);
    }

    /**
     * Get creation time
     * @return string|null
     */
    public function getCreationTime()
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * @param int $cardId
     *
     * @return \POSIMWebExt\GCLink\Api\Data\CardsInterface
     */
    public function setId($cardId)
    {
        return $this->setData(self::CARD_ID, $cardId);
    }

    /**
     * @param string $time
     *
     * @return \POSIMWebExt\GCLink\Api\Data\CardsInterface
     */
    public function setCreationTime($time)
    {
        return $this->setData(self::CREATION_TIME, $time);
    }

    /**
     * @param string $gcNum
     *
     * @return \POSIMWebExt\GCLink\Api\Data\CardsInterface
     */
    public function setGiftCardNum($gcNum)
    {
        return $this->setData(self::GC_NUM, $gcNum);
    }

    /**
     * @param float $balance
     *
     * @return \POSIMWebExt\GCLink\Api\Data\CardsInterface
     */
    public function setBalance($balance)
    {
        return $this->setData(self::BALANCE, $balance);
    }

    /**
     * @param $gcNum
     *
     * @return \Magento\Framework\DataObject
     */
    public function loadUsingGCNum($gcNum)
    {
        $gc = $this->getCollection()->addFieldToFilter('gc_num', $gcNum)->getFirstItem();

        return $gc;
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function setInvoiceId($id)
    {
        return $this->setData(self::INVOICE_ID, $id);
    }

    /**
     * @return mixed
     */
    public function getInvoiceId()
    {
        return $this->getData(self::INVOICE_ID);
    }

    /**
     * @param $amount
     *
     * @return $this
     */
    public function setGiftcardPurchase($amount)
    {
        return $this->setData(self::GIFTCARDCREDIT, $amount);
    }

    /**
     * @return mixed
     */
    public function getGiftcardPurchase()
    {
        return $this->getData(self::GIFTCARDCREDIT);
    }

    /**
     * @param $amount
     *
     * @return $this
     */
    public function setGiftcardPayment($amount)
    {
        return $this->setData(self::GIFTCARDDEBIT, $amount);
    }

    /**
     * @return $this|mixed
     */
    public function getGiftcardPayment()
    {
        return $this->setData(self::GIFTCARDDEBIT);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \POSIMWebExt\GCLink\Api\Data\CardsExtensionInterface $extensionAttributes
    )
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}