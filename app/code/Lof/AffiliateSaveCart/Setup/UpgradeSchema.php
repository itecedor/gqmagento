<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_AffiliateSaveCart
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    https://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\AffiliateSaveCart\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        //Update for version 1.0.1
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $table = $installer->getTable('lof_affiliatesavecart_savecart');

            $installer->getConnection()->addColumn(
                $table,
                'sent_email_count',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length'   => 11,
                    'default'   => 0,
                    'unsigned' => true,
                    'comment'  => 'Number Time To Send share save cart email.'
                ]
            );

        }

        $installer->endSetup();
    }
}