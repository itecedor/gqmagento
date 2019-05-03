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
namespace Lof\AffiliateSaveCart\Block\Adminhtml\Cart\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('cart_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Cart Affiliate Information'));

        $this->addTab(
            'detail_section',
            [
                'label' => __('Cart Detail'),
                'content' => $this->getLayout()->createBlock('Lof\AffiliateSaveCart\Block\Adminhtml\Cart\Edit\Tab\Detail')->toHtml()
            ]
        );
    }
}
