<?php
namespace POSIMWebExt\WebExtManager\Setup;
class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        //START: install stuff
        //END:   install stuff
        
//START table setup
$table = $installer->getConnection()->newTable(
            $installer->getTable('posimwebext_webextmanager_installed')
    )->addColumn(
            'posimwebext_webextmanager_installed_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true, ],
            'Entity ID'
        )->addColumn(
            'extension',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [ 'nullable' => false, ],
            'Extension'
        )->addColumn(
            'installed_version',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [ 'nullable' => false, ],
            'Installed Version'
        )->addColumn(
            'available_version',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [ 'nullable' => false, ],
            'Available Version'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [ 'nullable' => false, 'default' => '1', ],
            'Is Active'
        )->addIndex(
            $installer->getIdxName('posimwebext_webextmanager_installed',['extension'],\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
            ['extension'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]);
$installer->getConnection()->createTable($table);
//END   table setup
$installer->endSetup();
    }
}
