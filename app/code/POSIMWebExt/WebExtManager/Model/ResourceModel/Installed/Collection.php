<?php
namespace POSIMWebExt\WebExtManager\Model\ResourceModel\Installed;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('POSIMWebExt\WebExtManager\Model\Installed','POSIMWebExt\WebExtManager\Model\ResourceModel\Installed');
    }
}
