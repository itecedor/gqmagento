<?php
/**
 * @author     MageryThemes Team
 * @copyright  Copyright (c) 2018 MageryThemes (https://magery-themes.com)
 * @package    Magery_DisableReviews
 */

namespace Magery\DisableReviews\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NotFoundException;

class DisableRoute implements ObserverInterface
{
    /**
     * @var \Magery\DisableReviews\Helper\Data
     */
    private $helper;

    /**
     * @param \Magery\DisableReviews\Helper\Data $helper
     */
    public function __construct(
        \Magery\DisableReviews\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Forward review controller requests to no route page
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @throws NotFoundException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->reviewIsDisabled()) {
            throw new NotFoundException(__('Not Found'));
        }
    }
}
