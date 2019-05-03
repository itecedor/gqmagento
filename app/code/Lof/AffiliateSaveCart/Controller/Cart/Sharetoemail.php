<?php

namespace Lof\AffiliateSaveCart\Controller\Cart;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Lof\AffiliateSaveCart\Controller\AbstractCart;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;

class Sharetoemail extends AbstractCart
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    protected $_dataHelper;

    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Lof\AffiliateSaveCart\Helper\Data $helper,
        \Lof\AffiliateSaveCart\Model\AffiliateSaveCartFactory $saveCartFactory,
        DataPersistorInterface $dataPersistor,
        \Magento\Store\Model\StoreManager $storeManager,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $customerSession);
        $this->resultPageFactory = $resultPageFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->inlineTranslation    = $inlineTranslation;
        $this->transportBuilder    = $transportBuilder;
        $this->_dataHelper  =    $helper;
        $this->cartSave = $saveCartFactory;
        $this->dataPersistor = $dataPersistor;
        $this->storeManager        = $storeManager;
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        $data = $this->getRequest()->getPostValue();
        $redirectPath = '*/*/';
        try {
            if($data){
                $customer_email = isset($data['customer_email'])?$data['customer_email']:'';
                $save_cart_id = isset($data['cart_id'])?$data['cart_id']:'';
                $share_link = isset($data['share_link'])?$data['share_link']:'';
                $qrcode = isset($data['qrcode'])?$data['qrcode']:'';
                $subject = isset($data['subject'])?$data['subject']:'';
                $emails = isset($data['emails'])?$data['emails']:'';
                $message = isset($data['message'])?$data['message']:'';
                $customer_name = isset($data['customer_name'])?$data['customer_name']:$customer_email;
                $email_list = [];
                if($emails) {
                    $email_array = explode(",", $emails);
                    if($email_array) {
                        foreach($email_array as $email){
                            $email = trim($email);
                            if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $email_list[] = $email;
                            }
                        }
                    }
                }
                if($email_list && $customer_email) {
                    $store = $this->storeManager->getStore();
                    $cart_save = null;
                    $number_sent = 0;
                    if($save_cart_id) {
                        $cart_save = $this->cartSave->create()->load((int)$save_cart_id);
                        $number_sent = $cart_save->getSentEmailCount();
                    }
                    
                    $email_template_id = $this->_dataHelper->getConfig("affiliatesavecart/email_template");
                    $email_template_id = $email_template_id?$email_template_id:"affiliatesavecart_email_notify_template";
                    
                    $postdata = [];
                    $postdata['cart_id'] = $save_cart_id;
                    $postdata['share_link'] = $share_link;
                    $postdata['qrcode'] = $qrcode;
                    $postdata['subject'] = $subject;
                    $postdata['message'] = strip_tags($message);
                    $postdata['customer_name'] = $customer_name;
                    $postObject = new \Magento\Framework\DataObject();
                    $postObject->setData($postdata);
                    $fromAddress = ['email'=>$customer_email, 'name' => $customer_name];
                    foreach ($email_list as $k => $v) {
                        try {
                            $v = trim($v);
                            $transportBuilder = $this->transportBuilder
                            ->setTemplateIdentifier($email_template_id)
                            ->setTemplateOptions(
                                [
                                    'area'  => 'frontend',
                                    'store' => $store->getId()
                                ])
                            ->setTemplateVars(['data' => $postObject])
                            ->setFrom($fromAddress)
                            ->addTo($v);
                            $transport = $transportBuilder->getTransport();
                            try  {
                                $transport->sendMessage();
                                $this->inlineTranslation->resume();
                                $number_sent = (int)$number_sent + 1;
                                $this->messageManager->addSuccess(
                                    __('Sent email to %1', $v)
                                    );
                            } catch(\Exception $e) {
                                $error = true;
                                $this->messageManager->addError(
                                    __('An error when send email. We can\'t process your request right now. Sorry, that\'s all we know.')
                                    );
                                $this->messageManager->addError($e->getMessage());
                                $this->getDataPersistor()->set('affiliatesavecart', $this->getRequest()->getParams());
                            }
                        } catch (\Exception $e) {
                            $this->inlineTranslation->resume();
                            $this->messageManager->addError(
                                __('Errors when send emails. We can\'t process your request right now. Sorry, that\'s all we know.')
                                );
                            $this->messageManager->addError($e->getMessage());
                            $this->getDataPersistor()->set('affiliatesavecart', $this->getRequest()->getParams());
                        }
                    }
                    if($number_sent && $cart_save) {
                        try {
                            $cart_save->setData("sent_email_count", (int)$number_sent);
                            $cart_save->save();
                        }catch (\Exception $e) {
                            $this->messageManager->addError(
                                __('Errors when update save cart data.')
                                );
                            $this->messageManager->addError($e->getMessage());
                        }
                    } 
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addError('Could not send email save cart.');
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath($redirectPath);
    }
    /**
     * Get Data Persistor
     *
     * @return DataPersistorInterface
     */
    private function getDataPersistor()
    {
        if ($this->dataPersistor === null) {
            $this->dataPersistor = ObjectManager::getInstance()
                ->get(DataPersistorInterface::class);
        }

        return $this->dataPersistor;
    }
}
