<?php
namespace POSIMWebExt\WebExtManager\Model;
class Installed extends \Magento\Framework\Model\AbstractModel implements InstalledInterface, \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'posimwebext_webextmanager_installed';

    protected function _construct()
    {
        $this->_init('POSIMWebExt\WebExtManager\Model\ResourceModel\Installed');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
