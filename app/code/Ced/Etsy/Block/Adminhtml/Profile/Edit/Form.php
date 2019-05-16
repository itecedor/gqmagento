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
namespace Ced\Etsy\Block\Adminhtml\Profile\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\Form as DataForm;

/**
 * Class Form
 * @package Ced\Etsy\Block\Adminhtml\Profile\Edit
 */
class Form extends Generic
{
    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        /**
 * @var DataForm $form 
*/
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'class'=>"accordion",
                    'action' =>$this->getUrl(
                        '*/*/save',
                        [
                        'pcode' => $this->getRequest()->getParam('pcode', false),'section'=>'etsy_configuration'
                        ]
                    ), 'method' => 'post','enctype' => 'multipart/form-data'
                ]
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
