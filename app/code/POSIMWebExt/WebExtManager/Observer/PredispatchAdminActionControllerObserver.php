<?php

namespace POSIMWebExt\WebExtManager\Observer;

use \POSIMWebExt\WebExtManager\Helper\Data;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\App\Request\Http;

class PredispatchAdminActionControllerObserver implements ObserverInterface
{
    protected $webextHelper;
    protected $installedRepo;
    protected $installedFactory;

    public function __construct(Data $helper, \POSIMWebExt\WebExtManager\Model\InstalledFactory $installedFactory, \POSIMWebExt\WebExtManager\Model\InstalledRepository $installedRepo)
    {
        $this->webextHelper = $helper;
        $this->installedFactory = $installedFactory;
        $this->installedRepo = $installedRepo;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $available = $this->webextHelper->getAvailableVersions();
        foreach ($available as $new) {
            $ext = $this->installedFactory->create();
            $id = $this->installedRepo->getIDByName($new['ext']);
            if (null === $id || '' === $id || false === $id) {
                $ext->setExtension($new['ext']);
                $ext->save();
            }
            $id = $this->installedRepo->getIDByName($new['ext']);
            $ext->load($id);
            $ext->setAvailableVersion($new['version']);
            $ext->setInstalledVersion($new['installed']);

            if(version_compare($new['version'], $new['installed']) === 1){

                $this->webextHelper->addNotification($new['ext']);
            }



            $ext->save();

        }
    }
}