<?php

namespace POSIMWebExt\GCLink\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * Class UpgradeSchema
 * @package POSIMWebExt\GCLink\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $quoteTable = 'quote';
        $quoteAddressTable = 'quote_address';
        $orderTable = 'sales_order';
        $invoiceTable = 'sales_invoice';
        $creditmemoTable = 'sales_creditmemo';
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteTable),
                'posimgiftcard',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'default'  => '0.0000',
                    'comment'  => 'Posim Gift Card'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteTable),
                'posimgc_num',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length'   => '50',
                    'default'  => NULL,
                    'comment'  => 'POSIM Gift Card Number'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteAddressTable),
                'posimgiftcard',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'default'  => '0.0000',
                    'comment'  => 'Posim Gift Card'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteAddressTable),
                'posimgc_num',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length'   => '50',
                    'default'  => NULL,
                    'comment'  => 'POSIM Gift Card Number'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteAddressTable),
                'base_posimgiftcard',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'default'  => '0.0000',
                    'comment'  => 'Base Posim Gift Card'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteTable),
                'base_posimgiftcard',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'default'  => '0.0000',
                    'comment'  => 'Base Posim Gift Card'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'posimgiftcard',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'default'  => '0.0000',
                    'comment'  => 'Posim Gift Card'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'posimgc_num',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length'   => '50',
                    'default'  => NULL,
                    'comment'  => 'POSIM Gift Card Number'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'base_posimgiftcard',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'default'  => '0.0000',
                    'comment'  => 'Base Posim Gift Card'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($invoiceTable),
                'posimgiftcard',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'default'  => '0.0000',
                    'comment'  => 'Posim Gift Card'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($invoiceTable),
                'base_posimgiftcard',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'default'  => '0.0000',
                    'comment'  => 'Base Posim Gift Card'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($creditmemoTable),
                'posimgiftcard',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'default'  => '0.0000',
                    'comment'  => 'Posim Gift Card'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($creditmemoTable),
                'base_posimgiftcard',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'default'  => '0.0000',
                    'comment'  => 'Base Posim Gift Card'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable('posimwebext_gclink_cards'),
                'order_id',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'length'   => 20,
                    'default'  => NULL,
                    'comment'  => 'Last Order ID'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable('posimwebext_gclink_cards'),
                'giftcard_payment',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'default'  => NULL,
                    'comment'  => 'giftcard payment'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable('posimwebext_gclink_cards'),
                'giftcard_purchase',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length'   => '12,4',
                    'default'  => NULL,
                    'comment'  => 'Last Order ID'
                ]
            );
        $setup->endSetup();
    }
}
