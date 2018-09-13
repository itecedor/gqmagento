<?php
namespace POSIMWebExt\WebExtManager\Ui\Component\Listing\DataProviders\Posimwebext\Webextmanager\Installed;

class Listing extends \Magento\Ui\DataProvider\AbstractDataProvider
{    
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \POSIMWebExt\WebExtManager\Model\ResourceModel\Installed\CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }
}
