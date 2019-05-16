<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_Etsy
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Etsy\Block\Adminhtml\Profile\Edit\Tab\Attribute;

use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Backend\Block\Widget;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection;
use Magento\Framework\Data\Form\Element\AbstractElement;
/**
 * Class Requiredattribute
 * @package Ced\Etsy\Block\Adminhtml\Profile\Edit\Tab\Attribute
 */
class Requiredattribute extends Widget implements RendererInterface
{

    /**
     * @var string
     */
    public $_template = 'Ced_Etsy::profile/attribute/required_attribute.phtml';

    public $_objectManager;

    public $_coreRegistry;

    public $_profile;

    public $_etsyAttribute;

    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        Registry $registry,
        array $data = []
    )
    {
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $registry;
        $this->_profile = $this->_coreRegistry->registry('current_profile');
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        /*$button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            ['label' => __('Add Attribute'), 'onclick' => 'return requiredAttributeControl.addItem()', 'class' => 'add']
        );
        $button->setName('add_required_item_button');

        $this->setChild('add_button', $button);*/
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    /*public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }*/

    /**
     * @return array
     */
    public function getEtsyAttributes()
    {
        $requiredAttribute = [
            'Product Name' => ['etsy_attribute_name' => 'name', 'etsy_attribute_type' => 'text', 'magento_attribute_code' => 'name', 'required' => 1],
            'Price' => ['etsy_attribute_name' => 'price', 'etsy_attribute_type' => 'text', 'magento_attribute_code' => 'price', 'required' => 1],
            'Description' => ['etsy_attribute_name' => 'description', 'etsy_attribute_type' => 'textarea', 'magento_attribute_code' => 'description', 'required' => 1],
            'Image' => ['etsy_attribute_name' => 'image', 'etsy_attribute_type' => 'text', 'magento_attribute_code' => 'image', 'required' => 1],
            'Inventory And Stock' => ['etsy_attribute_name' => 'inventory', 'etsy_attribute_type' => 'text', 'magento_attribute_code' => 'quantity_and_stock_status', 'required' => 1]
        ];

        $this->_etsyAttribute[] = [
            'label' => __('Required Attributes'),
            'value' => $requiredAttribute
        ];

        return $this->_etsyAttribute;
    }

    /**
     * @return mixed
     */
    public function getMagentoAttributes()
    {
        $attributes = $this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection')
            ->getItems();

        $mattributecode = '--please select--';
        $mageAttrCodeArray[''] = $mattributecode;
        foreach ($attributes as $attribute) {
            $mageAttrCodeArray[$attribute->getAttributecode()] = $attribute->getFrontendLabel();
        }
        return $mageAttrCodeArray;
    }

    /**
     * @return array|mixed
     */
    public function getMappedAttribute()
    {
        $data = $this->_etsyAttribute[0]['value'];
        if ($this->_profile && $this->_profile->getId() > 0) {
            $data = json_decode($this->_profile->getProfileReqOptAttribute(), true);
            $data = $data['required_attributes'];
        }
        return $data;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }
}
