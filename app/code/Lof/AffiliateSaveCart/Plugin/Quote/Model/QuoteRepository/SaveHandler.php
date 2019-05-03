<?php

namespace Lof\AffiliateSaveCart\Plugin\Quote\Model\QuoteRepository;

use Lof\AffiliateSaveCart\Api\AffiliateSaveCartRepositoryInterface;
use Magento\Quote\Api\Data\CartExtensionFactory;
use Lof\AffiliateSaveCart\Api\Data\AffiliateSaveCartInterfaceFactory as AffiliateSaveCartFactory;
use Psr\Log\LoggerInterface as Logger;

class SaveHandler
{
    /**
     * @var AffiliateSaveCartRepositoryInterface
     */
    private $saveCartRepository;

    /**
     * @var CartExtensionFactory
     */
    private $cartExtensionFactory;

    private $resourceModel;

    private $saveCartFactory;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        AffiliateSaveCartRepositoryInterface $saveCartRepository,
        CartExtensionFactory $cartExtensionFactory,
        \Lof\AffiliateSaveCart\Model\ResourceModel\AffiliateSaveCart $resourceModel,
        AffiliateSaveCartFactory $saveCartFactory,
        Logger $logger
    ) {
        $this->saveCartRepository = $saveCartRepository;
        $this->cartExtensionFactory = $cartExtensionFactory;
        $this->resourceModel = $resourceModel;
        $this->saveCartFactory = $saveCartFactory;
        $this->logger = $logger;
    }

    public function afterSave($subject, $entity)
    {
        try {
            $extAttributes = $entity->getExtensionAttributes();
            if ($extAttributes && $extAttributes->getSaveCartData()) {
                $savedCart = $extAttributes->getSaveCartData();
                $this->saveCartRepository->save($savedCart);
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
        }

        return $entity;
    }
}
