<?php
namespace Milople\Requestforquote\Model\Config\Source;

class Emailsender implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'General Contact', 'label' => __('General Contact')],
            ['value' => 'Sales Representative', 'label' => __('Sales Representative')],
            ['value' => 'Customer Support', 'label' => __('Customer Support')],
        ];
    }
}