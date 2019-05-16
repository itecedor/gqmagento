<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement(EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_Etsy
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Etsy\Model\Config;

use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Ced\Etsy\Helper\Data;

/**
 * Class WhenMade
 *
 * @package Ced\Etsy\Model\Config
 */
class WhenMade implements ArrayInterface
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '2010_2017', 'label' => __('2010 - 2017')],
            ['value' => 'made_to_order', 'label' => __('Made To Order')],
            ['value' => '2000_2009', 'label' => __('2000 - 2009')],
            ['value' => '1998_1999', 'label' => __('1998 - 1999')],
            ['value' => 'before_1998', 'label' => __('before - 1998')],
            ['value' => '1990_1997', 'label' => __('1990 - 1997')],
            ['value' => '1980s', 'label' => __('1980s')],
            ['value' => '1970s', 'label' => __('1970s')],
            ['value' => '1960s', 'label' => __('1960s')]
        ];
    }
}
