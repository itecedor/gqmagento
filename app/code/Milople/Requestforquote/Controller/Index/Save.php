<?php
namespace Milople\Requestforquote\Controller\Index;
class Save extends \Magento\Framework\App\Action\Action
{
	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Filesystem\Io\File $filesystem,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Milople\Requestforquote\Model\Enquiry $postFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Mail\Template\TransportBuilder $transport,
		\Magento\Framework\Translate\Inline\StateInterface $stateInterface,
		\Milople\Requestforquote\Helper\Data $rfq,
        \Psr\Log\LoggerInterface $logger
    ) {
        
        $this->_request = $context->getRequest();
        $this->filesystem=$filesystem;
        $this->directory_list= $directory_list;
		$this->storeManager=$storeManager;
		$this->_transportBuilder=$transport;
		$this->inlineTranslation=$stateInterface;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_logger = $logger;
		$this->rfq=$rfq;
        $this->postFactory = $postFactory;
        return parent::__construct($context);
    }    
    public function execute()
    {
        $postdata = $this->_request->getPost();
        $name=$postdata['name'];
        $email=$postdata['email'];
        $country=$postdata['country'];
        $phone=$postdata['phone'];
        $description=$postdata['description'];
		$pname=$postdata['pname'];
		$purl=$postdata['purl'];
        $model= $this->postFactory;
        // Add new data
                 $model->setData(
                 array(
                  'name'=>$name,
                  'email'=>$email,
                  'contact_no' => $phone,
                  'country' => $country,
				  'description' => $description,
				  'product_name' => $pname
                  ));
                  $model->save(); 		
		//Email functionality
		$enable_ack = $this->rfq->getConfig('requestforquote/enquiry_acknowledgement_settings/send_acknowledgement');
		$enquiry_to = $this->rfq->getConfig('requestforquote/tag_setting_group/send_enquiry_emails_to');
		$sender_name = $this->rfq->getConfig('requestforquote/enquiry_acknowledgement_settings/sender_name');
		$ack_subject = $this->rfq->getConfig('requestforquote/enquiry_acknowledgement_settings/subject');
		if($enable_ack==true)
		{
			$ack_msg = $this->rfq->getConfig('requestforquote/enquiry_acknowledgement_settings/message');
			$ack_mail_sender = $this->rfq->getConfig('requestforquote/enquiry_acknowledgement_settings/email_sender');
			$templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->storeManager->getStore()->getId());
			$from = array('email' => $ack_mail_sender, 'name' => $sender_name);
			$templateVars = array(
								'store' => $this->storeManager->getStore(),
								'customer_name' => $name,
								'message'   => $ack_msg,
								'customSubject' => $ack_subject,
								'nameofproduct' => $pname,
								'urlofproduct' => $purl
							);
			  $to = array($email);
			 try{
			 $transport = $this->_transportBuilder->setTemplateIdentifier('mymodule_email_template')
				  ->setTemplateOptions($templateOptions)
				  ->setTemplateVars($templateVars)
				  ->setFrom($from)
				  ->addTo($to);  
			 
			  $transport->getTransport()->sendMessage();
			   } catch (\Exception $e) {            
				$this->messageManager->addError($e->getMessage());
			}
		}
		//mail to store owner
		$templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->storeManager->getStore()->getId());
			$from = array('email' => $email, 'name' => $name);
			$templateVars = array(
								'store' => $this->storeManager->getStore(),
								'customer_name' => $sender_name,
								'message'   => $description,
								'customSubject' => $ack_subject,
								'nameofproduct' => $pname,
								'urlofproduct' => $purl
							);
			  $to = array($enquiry_to);
			 try{
			 $transport = $this->_transportBuilder->setTemplateIdentifier('mymodule_email_template')
				  ->setTemplateOptions($templateOptions)
				  ->setTemplateVars($templateVars)
				  ->setFrom($from)
				  ->addTo($to);  
			 
			  $transport->getTransport()->sendMessage();
			   } catch (\Exception $e) {            
				$this->messageManager->addError($e->getMessage());
			}
	}
}	