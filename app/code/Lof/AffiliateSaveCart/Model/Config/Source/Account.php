<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://venustheme.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Venustheme
 * @package    Lof_AffiliateSaveCart
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Lof\AffiliateSaveCart\Model\Config\Source;

class Account implements \Magento\Framework\Option\ArrayInterface
{

    protected $_resource;
    protected $_resourceModel;
    /**
     * @param GroupManagementInterface $groupManagement
     * @param \Magento\Framework\Convert\DataObject $converter
     */
    public function __construct(
        \Lof\AffiliateSaveCart\Model\ResourceModel\AffiliateSaveCart $resource = null,
        \Magento\Framework\App\ResourceConnection $resourceModel
    ) {
        $this->_resource = $resource;
        $this->_resourceModel = $resourceModel;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = array();
        $table_name = $this->_resourceModel->getTableName('lof_affiliate_account');
        $connection = $this->_resource->getConnection();
        $select = $connection->select()->from(
            ['ce' => $table_name],
            ['customer_id', 'fullname']
        );
        $rows = $connection->fetchAll($select);

        foreach ($rows as $key => $result) {
            $data[$key]['label'] = $result['fullname'];
            $data[$key]['value'] =  $result['customer_id'];
        }
        return $data;
    }
}