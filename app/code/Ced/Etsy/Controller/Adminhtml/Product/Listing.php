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

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Listing extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Ced_Etsy::product';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * @var \Ced\Etsy\Helper\Data
     */
    public $helper;
    /**
     * @var Filesystem
     */
    public $filesystem;

    /**
     * Listing constructor.
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Ced\Etsy\Helper\Data $data,
        Filesystem $filesystem,
        Filesystem\Io\File $file
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->helper = $data;
        $this->filesystem = $filesystem;
        $this->file = $file;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $folderPath = $this->filesystem->getDirectoryRead(DirectoryList::APP)->getAbsolutePath('code/Ced/Etsy/Setup/json/');
        $shopName = $this->scopeConfig->getValue('etsy_config/etsy_setting/shop_name');
        if (!$shopName) {
            $this->messageManager->addErrorMessage("please enter the shop name in configuration");
            return $this->_redirect('etsy/product/index');
        }
        try {            
            for ($i=1; $i < 10; $i++) { 
                $results = $this->helper->ApiObject()->findAllShopListingsActive(
                    [
                        'params' => [
                            'shop_id' => $shopName,
                            'limit' => 50,
                            'page' => $i
                        ]
                    ]
                );
                $path = $folderPath . 'products-'.$i.'.json';
                if (isset($results['results'])) {
                    $file = fopen($path, 'w+');
                    fwrite($file, json_encode($results['results']));
                    fclose($file);
                    chmod($path, 0777);
                } else {
                    $this->messageManager->addErrorMessage("Active Listing on Etsy is null");
                }
            }               
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage("invalid auth Ids");
        }
        return $this->_redirect('etsy/product/index');
    }
}
