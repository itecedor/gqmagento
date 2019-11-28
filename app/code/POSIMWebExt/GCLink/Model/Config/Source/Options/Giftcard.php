<?php

namespace POSIMWebExt\GCLink\Model\Config\Source\Options;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Giftcard extends AbstractSource
{
    /**
     * @return array
     */
    public function getAllOptions()
    {
        $options = [
            0 => [
                'label' => 'Please Select',
                'value' => NULL
            ],
            1 => [
                'label' => 'Both Physical and Virtual',
                'value' => 1
            ],
            2 => [
                'label' => 'Virtual (email)',
                'value' => 2
            ],
            3 => [
                'label' => 'Physical (snail mail)',
                'value' => 3
            ],
        ];

        return $options;
    }
}