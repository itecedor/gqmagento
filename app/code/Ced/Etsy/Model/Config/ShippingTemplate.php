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

namespace Ced\Etsy\Model\Config;

use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Message\Manager;
use Ced\Etsy\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem\Io\File;

/**
 * Class ShippingTemplate
 *
 * @package Ced\Etsy\Model\Config
 */
class ShippingTemplate implements ArrayInterface
{
    /**
     * @var ObjectManagerInterface
     */
    public $objectManager;

    /**
     * @var Filesystem
     */
    public $filesystem;
    /**
     * @var Manager
     */
    public $messageManager;
    /**
     * @var Data
     */
    public $helper;
    /**
     * @var Data
     */
    public $scopeConfig;

    /**
     * @var File
     */
    public $file;

    /**
     * @var ScopeConfigInterface
     */
    public $scopeConfigManager;

    /**
     * ShippingTemplate constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param Filesystem $filesystem
     * @param Data $helper
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Filesystem $filesystem,
        Manager $manager,
        Data $helper,
        ScopeConfigInterface $scopeConfig,
        File $file
    )
    {
        $this->objectManager = $objectManager;
        $this->filesystem = $filesystem;
        $this->messageManager = $manager;
        $this->helper = $helper;
        $this->scopeConfigManager = $scopeConfig;
        $this->file = $file;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $shippingTemplates = [];
        $folderPath = $this->filesystem->getDirectoryRead(DirectoryList::APP)
            ->getAbsolutePath('code/Ced/Etsy/Setup/json/');
        $path = $folderPath . 'ShippingTemplate.json';
        $userName = $this->scopeConfigManager->getValue('etsy_config/etsy_setting/user_name');
        if ($userName) {
            if (!$this->file->fileExists($path)) {
                try {
                    $shipTemp = $this->helper->ApiObject()->findAllUserShippingProfiles(
                        ['params' => ['user_id' => $userName]]
                    );
                    if (isset($shipTemp['results'])) {
                        $options[] = [
                            'value' => "",
                            'label' => "Please select the shipping templates"
                        ];
                        $shippingTemplates = $shipTemp['results'];
                        $file = fopen($path, 'w+');
                        fwrite($file, json_encode($shippingTemplates));
                        fclose($file);
                        chmod($path, 0777);
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage('Invalid Configuration Details');
                    $options[] = [
                        'value' => "",
                        'label' => "Please fill Correct User Name"
                    ];
                }
            } else {                
                $options[] = [
                    'value' => "",
                    'label' => "Please select the shipping templates"
                ];
                $shippingTemplates = $this->file->read($path);
                if ($shippingTemplates != '') {
                    $shippingTemplates = json_decode($shippingTemplates, true);
                } else {
                    $shippingTemplates = [];
                }
            }
            foreach ($shippingTemplates as $value) {
                $options[] = [
                    'value' => $value['shipping_template_id'],
                    'label' => $value['title']
                ];
            }
        } else {
            $options[] = [
                'value' => "",
                'label' => "Please fill the User Name"
            ];
        }
        return $options;
    }
}
