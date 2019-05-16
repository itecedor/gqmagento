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
use Ced\Etsy\Helper\Data;
use Ced\Etsy\Helper\Etsy;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\Product;
use Ced\Etsy\Model\Profileproducts;

class ProductUpload extends Action
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
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    public $filter;

    /**
     * @var Data
     */
    public $helper;

    /**
     * @var Etsy
     */
    public $etsy;
    /**
     * @var Product
     */
    public $catalogCollection;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Filter $filter,
        Data $helper,
        Etsy $etsy,
        Product $collection,
        Profileproducts $profileproducts
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->filter = $filter;
        $this->helper = $helper;
        $this->etsy = $etsy;
        $this->catalogCollection = $collection;
        $this->profileproducts = $profileproducts;
    }

    /**
     * Product Upload Action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $listingID = '';
        $pictureUrl = [];
        $collection = $this->filter->getCollection($this->_objectManager->create('Magento\Catalog\Model\Product')->getCollection());
        $ids = $collection->getAllIds();
        $error = '';
        $skus = "";
        try {
            foreach ($ids as $id) {
                $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($id);
                if ($product->getEtsyListingId()) {
                    $error .= "SKU: " . $product->getSku()." alredy uploaded on Etsy";
                    continue;
                }
                $finaldata = $this->etsy->prepareData($id);
                if ($finaldata['type'] == 'success') {
                    $response = $this->helper->ApiObject()->createListing(['data' => $finaldata['data']]);
                    if (isset($response['results'])) {
                        $listingID = $response['results'][0]['listing_id'];
                        $product->setEtsyProductStatus('uploaded');
                        $product->setEtsyListingId($listingID);
                        $product->save();
                        /*$allImg = $product->getMediaGallery('images');
                        foreach ($allImg as $value) {
                            $pictureUrl[] = '@'.$this->_objectManager->get('Magento\Catalog\Model\Product\Media\Config')->getMediaUrl($value['file']).';type=image/jpeg';
                        }
                        if (empty($pictureUrl)) {
                            $this->messageManager->addErrorMessage('image not found for '. $product->getSku());
                        } else if (!empty($listingID)) {
                            $args = [
                                'params' => [
                                    'listing_id' => $listingID
                                ],
                                'data' => [
                                    'image' => $pictureUrl
                                ]
                            ];
                            $response = $this->helper->ApiObject()->uploadListingImage($args);
                            echo '<pre>';
                            print_r($response);die;
                        }*/
                        $skus .= $product->getSku();
                    }
                } else {
                    $product->setEtsyProductStatus('invalidated');
                    $product->save();
                    $error .= $finaldata['data'] . " for SKU: " . $product->getSku();
                }
            }
            if ($error) {
                $this->messageManager->addErrorMessage($error);
            } 
            if ($skus){
                $this->messageManager->addSuccessMessage($skus.' are successfully uploaded');
            }
        } catch (\Exception $e) {
            //$e->getMessage()
            $this->messageManager->addErrorMessage("Exception due to wrong details");
        }
        $this->_redirect('etsy/product/index');
    }
}
