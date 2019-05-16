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

namespace Ced\Etsy\Model;

use \Magento\Framework\Model\AbstractModel;

class Profileproducts extends AbstractModel
{
    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init('Ced\Etsy\Model\ResourceModel\Profileproducts');
    }

    /**
     * @return $this
     */

    public function update()
    {
        $this->getResource()->update($this);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProductsCollection()
    {
        return $this->getResource('Ced\Etsy\Model\ResourceModel\Profileproducts\Collection');
    }

    /**
     * getting group vendors
     */
    public function getProfileProducts($profileId)
    {
        return $this->getResource()->getProfileProducts($profileId);
    }

    public function deleteFromProfile($productId)
    {
        $this->_getResource()->deleteFromProfile($productId);
        return $this;
    }

    public function profileProductExists($productId, $profileId)
    {
        $result = $this->_getResource()->profileProductExists($productId, $profileId);
        return ( is_array($result) && count($result) > 0 ) ? true : false;
    }

    /**
     * Load entity by attribute
     *
     * @param  string|array field
     * @param  null|string|array  $value
     * @param  string             $additionalAttributes
     * @return mixed
     */
    public function loadByField($field, $value, $additionalAttributes = '*')
    {
        $collection = $this->getResourceCollection()
            ->addFieldToSelect($additionalAttributes);
        if(is_array($field) && is_array($value)) {
            foreach($field as $key=>$f) {
                if(isset($value[$key])) {
                    //$f = $helper->getTableKey($f);
                    $collection->addFieldToFilter($f, $value[$key]);
                }
            }
        } else {
            $collection->addFieldToFilter($field, $value);
        }

        $collection->setCurPage(1)
            ->setPageSize(1);
        foreach ($collection as $object) {
            $this->load($object->getId());
            return $this;
        }
        return $this;
    }
}
