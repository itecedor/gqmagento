<?php

namespace Lof\AffiliateSaveCart\Controller\Cart;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Lof\AffiliateSaveCart\Controller\AbstractCart;
use Lof\AffiliateSaveCart\Api\AffiliateSaveCartRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Put extends AbstractCart
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var AffiliateSaveCartRepositoryInterface
     */
    private $saveCartRepository;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    protected $cartSave;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(
        CheckoutSession $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        AffiliateSaveCartRepositoryInterface $saveCartRepository,
        CustomerCart $cart,
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\AffiliateSaveCart\Model\AffiliateSaveCartFactory $saveCartFactory,
        ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($context, $customerSession);
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->formKeyValidator = $formKeyValidator;
        $this->saveCartRepository = $saveCartRepository;
        $this->cart = $cart;
        $this->cartSave = $saveCartFactory;
        $this->productRepository = $productRepository;
    }

    private function addToCart($cart_save)
    {
        $info_buyRequest = unserialize($cart_save->getQuoteItems());
        foreach ($info_buyRequest as $info) {
            $item = json_decode($info['info_buyRequest'], true);
            $item['qty'] = $info['qty'];
            if (isset($item['qty'])) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => $this->_objectManager->get(
                        \Magento\Framework\Locale\ResolverInterface::class
                    )->getLocale()]
                );
                $item['qty'] = $filter->filter($item['qty']);
            }

            $product = $this->_initProduct($item['product']);
            $related = isset($item['related_product']) ? $item['related_product'] : "";

            $this->cart->addProduct($product, $item);
            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }

            $this->cart->save();
        }
//        $this->cart->truncate()->save();
//
//        $this->saveCartRepository->setCurrentQuotePointer($quote->getCustomerId(), $quote->getId());
//        $this->checkoutSession->replaceQuote($quote);
//        $this->checkoutSession->setCartWasUpdated(true);
    }

    private function deleteCart($quote)
    {

        $customerId = $this->customerSession->getCustomerId();
        if ($quote->getCustomerId() == $customerId) {

            $extAttributes = $quote->getExtensionAttributes();
            if ($extAttributes) {
                $savedCart = $extAttributes->getSaveCartData();
                $this->saveCartRepository->delete($savedCart);
            }

            $quote->setCustomerId(new \Zend_Db_Expr('NULL'))
                ->setCustomerEmail(new \Zend_Db_Expr('NULL'))
                ->save();

            //clear quote if current
            $this->checkoutSession->clearQuote()->clearStorage();

        } else {
            throw NoSuchEntityException::doubleField('customerId', $customerId, 'quoteId', $quoteId);
        }
    }

    public function execute()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $cartAction = (string)$this->getRequest()->getParam('cart_action');
        $entityId = (int)$this->getRequest()->getParam('cart_id');
        $cart_save = $this->cartSave->create()->load($entityId);
        $data = $this->getRequest()->getPostValue();
        $quoteId = $data['quote_id'];

        try {
            $quote = $this->quoteRepository->get($quoteId);
            switch ($cartAction) {
                case 'delete_cart':
                    $this->deleteCart($quote);
                    $redirectPath = '*/*/';
                    break;
                case 'add_to_cart':
                    $this->addToCart($cart_save);
                    $redirectPath = 'checkout/cart';
                    break;
            }
        } catch (\Exception $e) {
            $this->messageManager->addError('Could not update shopping cart.');
            $redirectPath = '*/*/';
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath($redirectPath);
    }

    protected function _initProduct($productId)
    {
        if ($productId) {
            $storeId = $this->_objectManager->get(
                \Magento\Store\Model\StoreManagerInterface::class
            )->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }
}
