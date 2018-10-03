<?php
/**
 * @author     MageryThemes Team
 * @copyright  Copyright (c) 2018 MageryThemes (https://magery-themes.com)
 * @package    Magery_DisableReviews
 */

namespace Magery\DisableReviews\Observer;

use Magento\Framework\Event\ObserverInterface;

class RemoveBlocks implements ObserverInterface
{
    /**
     * @var \Magery\DisableReviews\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magery\DisableReviews\Helper\Data $helper
     */
    public function __construct(
        \Magery\DisableReviews\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Remove review-related blocks
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->reviewIsDisabled()) {
            /** @var \Magento\Framework\View\Layout\Interceptor $layout */
            $layout = $observer->getLayout();

            // Customer account section
            if ($this->helper->isCustomerAccountPage()) {
                foreach ($this->helper->getCustomerAccountBlocks() as $blockName) {
                    $layout->unsetElement($blockName);
                }
            }

            // Product page
            if ($layout->getBlock('product.info.review')) {
                $layout->unsetElement('product.info.review');
            }
        }
    }
}
