<?php

// @codingStandardsIgnoreFile

namespace Lof\AffiliateSaveCart\Plugin\Quote\Model\ResourceModel;

use \Lof\AffiliateSaveCart\Model\ResourceModel\AffiliateSaveCart as AffiliateSaveCartResource;

/**
 * Quote resource model
 */
class Quote
{
    /**
     * @var AffiliateSaveCartResource
     */
    private $resourceModel;

    public function __construct(
        AffiliateSaveCartResource $resourceModel
    ) {
        $this->resourceModel = $resourceModel;
    }

    public function aroundLoadByCustomerId($subject, callable $proceed, $quote, $customerId)
    {
        $this->resourceModel->loadByCustomerId($quote, $customerId);
        return $subject;
    }
}
