<?php
/**
 * @author     MageryThemes Team
 * @copyright  Copyright (c) 2018 MageryThemes (https://magery-themes.com)
 * @package    Magery_DisableReviews
 */

namespace Magery\DisableReviews\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var string
     */
    const XML_PATH_REVIEW_IS_DISABLED = 'catalog/review/is_disabled';

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\View\LayoutInterface $layout
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        parent::__construct($context);
        $this->layout = $layout;
    }

    /**
     * Checks if reviews feature is disabled for the current store
     *
     * @return bool
     */
    public function reviewIsDisabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_REVIEW_IS_DISABLED);
    }

    /**
     * Checks if we are in customer account section
     *
     * @return bool
     */
    public function isCustomerAccountPage()
    {
        return (false != $this->getCustomerAccountNavigationBlock());
    }

    /**
     * @return bool|\Magento\Framework\View\Element\BlockInterface
     */
    public function getCustomerAccountNavigationBlock()
    {
        return $this->layout->getBlock('customer_account_navigation');
    }

    /**
     * @return array
     */
    public function getCustomerAccountBlocks()
    {
        return [
            'customer_account_dashboard_info1',
            'customer-account-navigation-product-reviews-link'
        ];
    }
}
