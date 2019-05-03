<?php

namespace Lof\AffiliateSaveCart\Controller\Cart;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Lof\AffiliateSaveCart\Controller\AbstractCart;
use Lof\AffiliateSaveCart\Api\AffiliateSaveCartRepositoryInterface;

class Save extends AbstractCart
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var AffiliateSaveCartRepositoryInterface
     */
    private $saveCartRepository;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        AffiliateSaveCartRepositoryInterface $saveCartRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $customerSession);
        $this->checkoutSession = $checkoutSession;
        $this->saveCartRepository = $saveCartRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $quote = $this->checkoutSession->getQuote();
        $savedCart = $this->saveCartRepository->isQuoteSaved($quote->getId());
        if ($savedCart) {
            return $this->resultRedirectFactory->create()->setPath('*/*/edit');
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Save Cart'));
        $this->setActiveNav($resultPage);
        return $resultPage;
    }
}
