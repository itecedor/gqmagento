<?php

namespace POSIMWebExt\GCLink\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $table = $setup->getConnection()->newTable(
            $setup->getTable('posimwebext_gclink_cards')
        )->addColumn(
            'card_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            20,
            ['primary' => true, 'nullable' => false, 'auto_increment' => true],
            'Internal ID'
        )->addColumn(
            'balance',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            ['nullable' => false],
            'Balance'
        )->addColumn(
            'gc_num',
            \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
            50,
            ['unique'=> true, 'nullable' => false],
            'Gift Card Number'
        )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            [],
            'Creation Time'
        )->setComment(
            'Gift Card Table'
        )->addIndex(
            $setup->getIdxName(
                'posimwebext_gclink_cards',
                ['gc_num'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['gc_num'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        );
        $setup->getConnection()->createTable($table);
        $setup->endSetup();
    }
}