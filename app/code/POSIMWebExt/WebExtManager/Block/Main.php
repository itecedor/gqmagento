<?php
namespace POSIMWebExt\WebExtManager\Block;
use Magento\Framework\View\Element\Template;

class Main extends \Magento\Framework\View\Element\Template
{
    protected $installedFactory;

    public function __construct(\POSIMWebExt\WebExtManager\Model\InstalledFactory $installedFactory,
                                Template\Context $context,
                                array $data = [])
    {
        $this->installedFactory = $installedFactory;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout(){
        $installed = $this->installedFactory->create();
        $installed->setData('test', 'ttttttesssstttt')->save();
        exit();
    }
}
