<?php 
namespace Milople\Requestforquote\Model;
class Enquiry extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG = 'requestforquote_enquiries';

    protected $_cacheTag = 'requestforquote_enquiries';

    protected $_eventPrefix = 'requestforquote_enquiries';

    protected function _construct()
    {
        $this->_init('Milople\Requestforquote\Model\ResourceModel\Enquiry');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}