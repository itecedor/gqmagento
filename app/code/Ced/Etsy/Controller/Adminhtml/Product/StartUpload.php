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

namespace Ced\Etsy\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Ced\Etsy\Helper\Etsy;
use Ced\Etsy\Helper\Data;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class StartUpload
 * @package Ced\Etsy\Controller\Adminhtml\Product
 */
class StartUpload extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Ced_Etsy::product';
    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * @var JsonFactory
     */
    public $resultJsonFactory;

    /**
     * @var Etsy
     */
    public $etsy;

    /**
     * @var Data
     */
    public $helper;

    /**
     * StartUpload constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param Etsy $etsy
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        Etsy $etsy,
        Data $helper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->etsy = $etsy;
        $this->helper = $helper;
    }

    /**
     * @return string
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $message = [];
        $message['error'] = "";
        $message['success'] = "";

        $key = $this->getRequest()->getParam('index');
        $totalChunk = $this->_session->getProductChunks();
        $index = $key + 1;
        if (count($totalChunk) <= $index) {
            $this->_session->unsProductChunks();
        }
        if (isset($totalChunk[$key])) {
            $ids = $totalChunk[$key];

            foreach ($ids as $id) {

            }
        }
        return $resultJson->setData($message);
    }
}
