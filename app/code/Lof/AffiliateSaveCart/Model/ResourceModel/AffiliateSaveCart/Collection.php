<?php


namespace Lof\AffiliateSaveCart\Model\ResourceModel\AffiliateSaveCart;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Lof\AffiliateSaveCart\Model\AffiliateSaveCart', 'Lof\AffiliateSaveCart\Model\ResourceModel\AffiliateSaveCart');
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray('entity_id', 'title');
    }
}
