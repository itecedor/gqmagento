<?php

namespace Lof\AffiliateSaveCart\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;
use Lof\AffiliateSaveCart\Model\AffiliateSaveCart as AffiliateSaveCartModel;
use Lof\AffiliateSaveCart\Model\AffiliateSaveCartFactory as AffiliateSaveCartModelFactory;
use Lof\AffiliateSaveCart\Model\AffiliateSaveCartCustomerQuoteFactory as AffiliateSaveCartCustomerQuoteModelFactory;

/**
 * Class AffiliateSaveCartRepository
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AffiliateSaveCartRepository implements \Lof\AffiliateSaveCart\Api\AffiliateSaveCartRepositoryInterface
{
    /**
     * @var AffiliateSaveCartModel[]
     */
    protected $quotesById = [];

    /**
     * @var ResourceModel\AffiliateSaveCart
     */
    protected $resourceModel;

    /**
     * @var ResourceModel\AffiliateSaveCartCustomerQuote
     */
    protected $resourceCustomerQuote;

    /**
     * @var AffiliateSaveCartModelFactory
     */
    protected $saveCartModelFactory;

    /**
     * @var AffiliateSaveCartCustomerQuoteFactory
     */
    protected $saveCartCustomerQuoteModelFactory;

    /**
     * @param \Lof\AffiliateSaveCart\Model\ResourceModel\AffiliateSaveCart $resourceModel
     * @param \Lof\AffiliateSaveCart\Model\ResourceModel\AffiliateSaveCartCustomerQuote $resourceCustomerQuote
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Lof\AffiliateSaveCart\Model\ResourceModel\AffiliateSaveCart $resourceModel,
        \Lof\AffiliateSaveCart\Model\ResourceModel\AffiliateSaveCartCustomerQuote $resourceCustomerQuote,
        AffiliateSaveCartModelFactory $saveCartModelFactory,
        AffiliateSaveCartCustomerQuoteModelFactory $saveCartCustomerQuoteModelFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->resourceCustomerQuote = $resourceCustomerQuote;
        $this->saveCartModelFactory = $saveCartModelFactory;
        $this->saveCartCustomerQuoteModelFactory = $saveCartCustomerQuoteModelFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get($cartId)
    {
        if (!isset($this->quotesById[$cartId])) {
            $savedCart = $this->saveCartModelFactory->create();
            $this->resourceModel->load($savedCart, $cartId, 'quote_id');

            if (!$savedCart->getId()) {
                throw new NoSuchEntityException(__('Requested saved cart doesn\'t exist'));
            }

            $this->quotesById[$cartId] = $savedCart;
        }
        return $this->quotesById[$cartId];
    }

    /**
     * @inheritdoc
     */
    public function save(\Lof\AffiliateSaveCart\Api\Data\AffiliateSaveCartInterface $saveCart)
    {
        $quoteId = $saveCart->getQuoteId();
        try {
            $existingSavedCart = $this->get($quoteId);
            $existingSavedCart->setQuoteName($saveCart->getQuoteName());
            $existingSavedCart->setQuoteComment($saveCart->getQuoteComment());
            $existingSavedCart->setCustomerId($saveCart->getCustomerId());
        } catch (NoSuchEntityException $e) {
            $existingSavedCart = $saveCart;
        }

        unset($this->quotesById[$quoteId]);

        try {
            $this->resourceModel->save($existingSavedCart);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__('Unable to save cart'));
        }

        return $this->get($quoteId);
    }

    /**
     * @inheritdoc
     */
    public function setCurrentQuotePointer($customerId, $quoteId)
    {
        /**
         * @var AffiliateSaveCartCustomerQuote $customerQuote
         */
        $customerQuote = $this->saveCartCustomerQuoteModelFactory->create();
        $this->resourceCustomerQuote->load($customerQuote, $customerId, 'customer_id');

        $customerQuote->setCustomerId($customerId)
            ->setQuoteId($quoteId);

        $this->resourceCustomerQuote->save($customerQuote);
    }

    /**
     * @inheritdoc
     */
    public function getCurrentQuotePointer($customerId)
    {
        return $this->resourceCustomerQuote->getCurrentQuotePointer($customerId);
    }

    /**
     * @inheritdoc
     */
    public function removeCurrentQuotePointer($customerId)
    {
        return $this->resourceCustomerQuote->removeCurrentQuotePointer($customerId);
    }

    /**
     * @inheritdoc
     */
    public function isQuoteSaved($cartId)
    {
        try {
            return $this->get($cartId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(\Lof\AffiliateSaveCart\Api\Data\AffiliateSaveCartInterface $saveCart)
    {
        unset($this->quotesById[$saveCart->getId()]);

        try {
            $this->resourceModel->delete($saveCart);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __('Unable to remove saved cart')
            );
        }

        return true;
    }
}
