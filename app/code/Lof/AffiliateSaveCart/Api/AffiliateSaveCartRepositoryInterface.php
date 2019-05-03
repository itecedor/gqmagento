<?php

namespace Lof\AffiliateSaveCart\Api;

/**
 * Interface AffiliateSaveCartRepositoryInterface
 * @api
 */
interface AffiliateSaveCartRepositoryInterface
{
    /**
     * Enables an administrative user to return information for a specified cart.
     *
     * @param int $cartId
     * @return Data\AffiliateSaveCartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($cartId);

    /**
     * @param Data\AffiliateSaveCartInterface $saveCart
     * @return Data\AffiliateSaveCartInterface
     */
    public function save(Data\AffiliateSaveCartInterface $saveCart);

    /**
     * @param Data\AffiliateSaveCartInterface $saveCart
     * @return void
     */
    public function delete(Data\AffiliateSaveCartInterface $saveCart);

    /**
     * @param int $customerId
     * @return int
     */
    public function getCurrentQuotePointer($customerId);

    /**
     * @param int $customerId
     * @param int $quoteId
     * @return void
     */
    public function setCurrentQuotePointer($customerId, $quoteId);

    /**
     * @param int $customerId
     * @return int
     */
    public function removeCurrentQuotePointer($customerId);

    /**
     * @param int $cartId
     * @return bool|Data\AffiliateSaveCartInterface
     */
    public function isQuoteSaved($cartId);
}
