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
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Result\PageFactory;

class Additems extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Ced_Etsy::product';

    /**
     * @param \Magento\Backend\App\Action\Context        $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ScopeConfigInterface $scopeConfigManager
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->scopeConfigManager = $scopeConfigManager;
    }

    /**
     * Product Mass Upload 
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $helper =  $this->_objectManager->get('Ced\Etsy\Helper\Etsy');
        $result = $this->_objectManager->create('Ced\Etsy\Model\Profileproducts')->getCollection()->getData();
        foreach ($result as $val) {
            $ids[] = $val['product_id'];
        }
        $batchSize = 5;//$this->scopeConfigManager->getValue('etsy_config/product_upload/chunk_size');

        if (!empty($ids)) {
            $productids = array_chunk($ids, $batchSize);
            $this->_objectManager->create('Magento\Backend\Model\Session')->setProductChunks($productids);
            $resultPage = $this->resultPageFactory->create();
            $resultPage->setActiveMenu('Ced_Etsy::product');
            $resultPage->getConfig()->getTitle()->prepend(__('Add Products On Etsy'));
            return $resultPage;
        } else {
            $this->messageManager->addErrorMessage(__('No product available for upload.'));
            $this->_redirect('etsy/product/index');
        }
    }
}
