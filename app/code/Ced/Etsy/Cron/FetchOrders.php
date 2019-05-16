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
namespace Ced\Etsy\Cron;

use Ced\Etsy\Helper\Order;

/**
 * Class FetchOrders
 * @package Ced\Etsy\Cron
 */
class FetchOrders
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    public $logger;

    /**
     * @var Order
     */
    public $order;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        Order $order
    ) {
        $this->logger = $logger;
        $this->order = $order;
    }

    /**
     * @return bool|\Magento\Framework\Message\ManagerInterface
     */
    public function execute()
    {
        $order = $this->order->hasNewOrders();
        $this->logger->info('fetch orders Cron for Etsy run successfully');
        return  $order;
    }
}
