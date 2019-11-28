<?php

namespace POSIMWebExt\GCLink\Model\ResourceModel\Cards;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'card_id';

    protected function _construct()
    {
        $this->_init('POSIMWebExt\GCLink\Model\Cards', 'POSIMWebExt\GCLink\Model\ResourceModel\Cards');
    }
}