<?php

namespace POSIMWebExt\GCLink\Model\Product\Type;

use POSIMWebExt\GClink\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;

class Giftcard extends \Magento\Catalog\Model\Product\Type\Simple
{
    const TYPE_GIFTCARD = 'posimgc';
    protected $productRepository;
    protected $productAttributeOptionRepository;
    protected $catalogSession;

    public function __construct(\Magento\Catalog\Model\Product\Option $catalogProductOption,
                                \Magento\Eav\Model\Config $eavConfig,
                                \Magento\Catalog\Model\Product\Type $catalogProductType,
                                \Magento\Framework\Event\ManagerInterface $eventManager,
                                \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb,
                                \Magento\Framework\Filesystem $filesystem,
                                \Magento\Framework\Registry $coreRegistry,
                                \Psr\Log\LoggerInterface $logger,
                                ProductRepositoryInterface $productRepository,
                                \Magento\Catalog\Api\ProductAttributeOptionManagementInterface $productAttributeOptionRepository,
                                \Magento\Catalog\Model\Session $catalogSession)
    {
        $this->productRepository = $productRepository;
        $this->productAttributeOptionRepository = $productAttributeOptionRepository;
        $this->catalogSession = $catalogSession;
        parent::__construct($catalogProductOption, $eavConfig, $catalogProductType, $eventManager, $fileStorageDb, $filesystem, $coreRegistry, $logger, $productRepository);
    }

    /**
     * Delete data specific for Simple product type
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return void
     */
    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product)
    {
    }

    /**
     * @param \Magento\Catalog\Model\Product\Interceptor|null $product
     *
     * @return bool
     */
    public function isVirtual($product = NULL)
    {
        if (!is_null($product)) {
            $setType = $product->getAttributeText('posimgc_type');
            if ($setType == NULL) {
                $productOption = $this->productRepository->getById($product->getId())->getCustomAttribute('posimgc_type')->getValue();
                $attributeValues = $this->productAttributeOptionRepository->getItems('posimgc_type');
                foreach ($attributeValues as $value) {
                    if ($value->getValue() == $productOption) {
                        $setType = $value->getLabel();
                        break;
                    }
                }
            }
            if ($setType == 'Virtual (email)') {
                return true;
            } elseif ($setType == 'Both Physical and Virtual') {
                $giftCardSettings = $this->catalogSession->getData('posimgc_settings');
                if ($giftCardSettings['type'] == 'virtual') {
                    return true;
                } else {
                    return false;
                }
            } elseif ($product->getData('gctype') == 'virtual') {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public
    function canConfigure($product = NULL)
    {
        return true;
    }
}
