<?php

namespace POSIMWebExt\GCLink\Api\Data;

interface CardsInterface
{
    const CARD_ID = 'card_id';
    const GC_NUM = 'gc_num';
    const BALANCE = 'balance';
    const CREATION_TIME = 'creation_time';
    const INVOICE_ID = 'order_id';
    const GIFTCARDCREDIT = 'giftcard_purchase';
    const GIFTCARDDEBIT = 'giftcard_payment';

    /**
     * Get card id
     * @return int
     */
    public function getId();

    /**
     * Get gift card balance
     * @return float
     */
    public function getBalance();

    /**
     * Get creation time
     * @return string
     */
    public function getCreationTime();

    /**
     * Get gift card number
     * @return string
     */
    public function getGiftCardNum();

    /**
     * Set card ID
     *
     * @param $cardId
     *
     * @return \POSIMWebExt\GCLink\Api\Data\CardsInterface
     */
    public function setId($cardId);

    /**
     * Set card balance
     *
     * @param $balance
     *
     * @return \POSIMWebExt\GCLink\Api\Data\CardsInterface
     */
    public function setBalance($balance);

    /**
     * Set creation Time
     *
     * @param $time
     *
     * @return \POSIMWebExt\GCLink\Api\Data\CardsInterface
     */
    public function setCreationTime($time);

    /**
     * Set gift card number
     *
     * @param $gcNum
     *
     * @return \POSIMWebExt\GCLink\Api\Data\CardsInterface
     */
    public function setGiftCardNum($gcNum);

    /**
     * @param $id
     *
     * @return mixed
     */
    public function setInvoiceId($id);

    /**
     * @return mixed
     */
    public function getInvoiceId();

    /**
     * @param $amount
     *
     * @return mixed
     */
    public function setGiftcardPayment($amount);

    /**
     * @return mixed
     */
    public function getGiftcardPayment();

    /**
     * @param $amount
     *
     * @return mixed
     */
    public function setGiftcardPurchase($amount);

    /**
     * @return mixed
     */
    public function getGiftcardPurchase();
}