<?php
namespace POSIMWebExt\WebExtManager\Model\ResourceModel;
class Installed extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('posimwebext_webextmanager_installed','posimwebext_webextmanager_installed_id');
    }

    public function getIDByName($name)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'posimwebext_webextmanager_installed_id')
            ->where('extension = :extension');
        $binds = ['extension' => $name];
        return $adapter->fetchOne($select, $binds);
    }

}
