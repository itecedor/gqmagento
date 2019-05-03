<?php

namespace Lof\AffiliateSaveCart\Controller\Cart;

//use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\TestFramework\Inspection\Exception;

class AddCart extends \Magento\Framework\App\Action\Action
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    protected $cartSave;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\AffiliateSaveCart\Model\AffiliateSaveCartFactory $saveCartFactory,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Registry $registry,
        CustomerCart $cart
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->cartSave = $saveCartFactory;
        $this->productRepository = $productRepository;
        $this->_coreRegistry        = $registry;
        $this->cart = $cart;
    }

    public function execute()
    {
        $cart_save = $this->_coreRegistry->registry('current_savecart');
        if($cart_save && $cart_save->getId()) {
            $entityId = $cart_save->getId();
        } else {
            $entityId = (int)$this->getRequest()->getParam('cart_id');
            $cart_save = $this->cartSave->create()->load($entityId);
        }

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
            try {
                $this->cart->addProduct($product, $item);
            } catch (\Exception $e){
            }
            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }

        }
        $this->cart->save();
        $quote = $this->cart->getQuote();
        $quote->setSavecartId($entityId)->save();

        $redirectPath = 'checkout/cart';
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