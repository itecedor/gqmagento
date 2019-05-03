<?php

namespace Lof\AffiliateSaveCart\Plugin\Checkout\Model;

use Lof\AffiliateSaveCart\Api\AffiliateSaveCartRepositoryInterface;

class Cart
{
    /**
     * @var AffiliateSaveCartRepositoryInterface
     */
    private $saveCartRepository;

    public function __construct(
        AffiliateSaveCartRepositoryInterface $saveCartRepository
    ) {
        $this->saveCartRepository = $saveCartRepository;
    }

    /**
     * @param \Magento\Checkout\Model\Cart $subject
     * @param callable $proceed
     * @return mixed
     */
    public function aroundTruncate($subject, callable $proceed)
    {
        $quote = $subject->getQuote();
        $savedCart = $this->saveCartRepository->isQuoteSaved($quote->getId());
        if ($savedCart) {
            $this->saveCartRepository->removeCurrentQuotePointer($quote->getCustomerId());
            $subject->getCheckoutSession()
                ->clearQuote()
                ->clearStorage();
            $subject->unsetData('quote');
            return $subject;
        } else {
            return $proceed();
        }
    }
}
