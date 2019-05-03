<?php

namespace Lof\AffiliateSaveCart\Controller\Cart;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Lof\AffiliateSaveCart\Api\AffiliateSaveCartRepositoryInterface;
use Lof\AffiliateSaveCart\Model\AffiliateSaveCartFactory as AffiliateSaveCartModelFactory;
use Magento\Framework\App\RequestInterface;

class UpdatePost extends \Magento\Checkout\Controller\Cart
{
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var AffiliateSaveCartRepositoryInterface
     */
    private $saveCartRepository;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var AffiliateSaveCartModelFactory
     */
    private $saveCartModelFactory;

    protected $helper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        CartRepositoryInterface $quoteRepository,
        AffiliateSaveCartRepositoryInterface $saveCartRepository,
        \Magento\Customer\Model\Session $customerSession,
        AffiliateSaveCartModelFactory $saveCartModelFactory,
        \Lof\Affiliate\Helper\Data $helper
    ) {
        parent::__construct($context, $scopeConfig, $checkoutSession, $storeManager, $formKeyValidator, $cart);
        $this->quoteRepository = $quoteRepository;
        $this->saveCartRepository = $saveCartRepository;
        $this->customerSession = $customerSession;
        $this->saveCartModelFactory = $saveCartModelFactory;
        $this->helper = $helper;
    }

    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->customerSession->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }

    /**
     * Update customer's shopping cart
     *
     * @return void
     */
    protected function _updateShoppingCart()
    {
        try {
            $cartData = $this->getRequest()->getParam('cart');
            if (is_array($cartData)) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                    }
                }
                if (!$this->cart->getCustomerSession()->getCustomerId() && $this->cart->getQuote()->getCustomerId()) {
                    $this->cart->getQuote()->setCustomerId(null);
                }

                $cartData = $this->cart->suggestItemsQty($cartData);
                $this->setSaveCartData($cartData);
                $this->cart->updateItems($cartData);
                    //->save();
                //avoid call save method because it update current quote
                $this->cart->getQuote()->getBillingAddress();
                $this->cart->getQuote()->getShippingAddress()->setCollectShippingRates(true);
                $this->cart->getQuote()->collectTotals();

                $this->quoteRepository->save($this->cart->getQuote());

                $quote = $this->cart->getQuote();
                $savedCart = $this->saveCartRepository->get($quote->getId());
                $quoteItems = $quote->getAllItems();
                $items = [];
                foreach ($quoteItems as $quoteItem)  {
//            $item['product_id'] = $quoteItem->getProductId();
                    if ($quoteItem->getParentItemId()) continue;
                    $item['qty'] = $quoteItem->getQty();
                    $item['info_buyRequest'] = $quoteItem->getOptionByCode('info_buyRequest')->getValue();
//            $item = json_encode($items);
                    array_push($items, $item);
                }
                $items = serialize($items);

                $commission_action = $this->helper->getConfig('affiliatesavecart/commission_action');
                $commission = $this->helper->getConfig('affiliatesavecart/commission');
                $subtotal = $quote->getSubtotal();
                if ($commission_action == 1) $quote_commmission = $commission;
                else $quote_commmission = $commission*$subtotal/100;
                if ((int)$quote_commmission > 0) $savedCart->setCommission($quote_commmission);
                $savedCart->setQuoteItems($items)->save();
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError(
                $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
            );
        }
    }

    protected function setSaveCartData($cartData)
    {
        $quote = $this->cart->getQuote();
        try {
            $savedCart = $this->saveCartRepository->get($quote->getId());
        } catch (NoSuchEntityException $e) {
            $savedCart = $this->saveCartModelFactory->create();
            $savedCart->setQuoteId($quote->getId());
        }
        $savedCart->setQuoteName($cartData['quote_name']);
        $savedCart->setQuoteComment($cartData['quote_comment']);
        $savedCart->setCustomerId($quote->getCustomerId());
        $quoteItems = $quote->getAllItems();
        $items = [];
        foreach ($quoteItems as $quoteItem)  {
            if ($quoteItem->getParentItemId()) continue;
//            $item['product_id'] = $quoteItem->getProductId();
            $item['qty'] = $quoteItem->getQty();
            $item['info_buyRequest'] = $quoteItem->getOptionByCode('info_buyRequest')->getValue();
            array_push($items, $item);
        }
        $items = serialize($items);
        $savedCart->setQuoteItems($items);

        $extensionAttributes = $quote->getExtensionAttributes();

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->cartExtensionFactory->create();
        }

        $extensionAttributes->setSaveCartData($savedCart);
        $quote->setExtensionAttributes($extensionAttributes);
    }

    /**
     * Update shopping cart data action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $updateAction = (string)$this->getRequest()->getParam('update_cart_action');
        $quoteId = $this->getRequest()->getParam('quote_id');

        try {
            $quote = $this->quoteRepository->get($quoteId);
            if ($quote->getCustomerId() == $this->customerSession->getCustomerId()) {
                $this->cart->setQuote($quote);
                $this->_updateShoppingCart();

                switch ($updateAction) {
                    case 'save':
                        $this->_checkoutSession->clearQuote()->clearStorage();
                        $this->messageManager->addSuccessMessage(
                            __('The main cart contents has been transferred to the quote.')
                        );
                        $resultRedirect = $this->resultRedirectFactory->create();
                        $resultRedirect->setPath('affiliatesavecart/cart');
                        return $resultRedirect;
                    case 'update':
                        $this->messageManager->addSuccessMessage(
                            __('Quote has been updated.')
                        );
                        break;
                }
            }
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addException($e, __('We can\'t update the shopping cart.'));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t update the shopping cart.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }

        return $this->_goBack();
    }
}
