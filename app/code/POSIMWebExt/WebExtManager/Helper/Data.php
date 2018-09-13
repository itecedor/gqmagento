<?php

namespace POSIMWebExt\WebExtManager\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use \Magento\Framework\Module\ModuleListInterface;

class Data extends AbstractHelper
{
    /**
     * @var ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $dir;

    /**
     * @var \Magento\Setup\Test\Unit\Console\Command\UpgradeCommandTest
     */
    protected $installer;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $cachePool;

    /**
     * @var \Magento\AdminNotification\Model\InboxFactory
     */
    protected $inbox;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $adminHelper;

    /**
     * @var \POSIMWebExt\WebExtManager\Logger\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;


    /**
     * Data constructor.
     * @param \Magento\AdminNotification\Model\InboxFactory $inboxFactory
     * @param \POSIMWebExt\WebExtManager\Logger\Logger $logger
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Backend\Helper\Data $helperBackend
     * @param \Magento\Setup\Test\Unit\Console\Command\UpgradeCommandTest $installer
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param ModuleListInterface $moduleList
     * @param Context $context
     */
    public function __construct(
        \Magento\AdminNotification\Model\InboxFactory $inboxFactory,
        \POSIMWebExt\WebExtManager\Logger\Logger $logger,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Backend\Helper\Data $helperBackend,
        \Magento\Setup\Test\Unit\Console\Command\UpgradeCommandTest $installer,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\App\ResourceConnection $resource,
        ModuleListInterface $moduleList,
        Context $context)
    {
        $this->resource = $resource;
        $this->date = $date;
        $this->inbox = $inboxFactory;
        $this->cachePool = $cacheFrontendPool;
        $this->cacheTypeList = $cacheTypeList;
        $this->installer = $installer;
        $this->dir = $dir;
        $this->moduleList = $moduleList;
        $this->adminHelper = $helperBackend;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * getInstalledVersions
     * gets the version of all installed posim extensions
     * @return array
     */
    public function getInstalledVersions()
    {
        $posimWebExt = array();
        $extensions = $this->moduleList->getNames();
        foreach ($extensions as $ext) {
            if (false !== strpos($ext, 'POSIMWebExt')) {
                $posimWebExt[] = array('ext' => $ext, 'version' => $this->getInstalledVersion($ext));
            }
        }

        return $posimWebExt;
    }

    /**
     * getAvailableVersions()
     * gets the most recent versions for all installed posim extensions
     * @return array
     */
    public function getAvailableVersions()
    {
        $installed = $this->getInstalledVersions();
        $avail = array();
        foreach ($installed as $ext) {
            $url = $this->getVersionsUrl($ext['ext']);
            $availableVersion = $this->readVersion($url);
            if ($availableVersion) {
                $avail[] = array(
                    'ext' => $ext['ext'],
                    'version' => $availableVersion,
                    'installed' => $ext['version']
                );
            }

        }
        return $avail;
    }

    /**
     * readVersion
     * reads the latest available version of given extension version url from posim.com
     * @param $url
     * @return bool|float
     */
    protected function readVersion($url)
    {
        $json_file = @fopen($url, 'r');

        if ($json_file) {
            $string = file_get_contents($url);
            $json = json_decode($string, true);
            $newest = 0.0;
            foreach ($json['versions'] as $version) {
                if ($newest <= (float)$version) {
                    $newest = $version;
                }
            }
            return $newest;
        } else {
            return false;
        }
    }

    /**
     * getInstalledVersion
     * gets the installed version of one extension
     * @param $name
     * @return mixed
     */
    protected function getInstalledVersion($name)
    {
        return $this->moduleList->getOne($name)['setup_version'];
    }

    /**
     * getAvailableVersion
     * gets the most recent version for one extension
     * @param string $name
     */
    public function getAvailableVersion($name)
    {
        $url = $this->getVersionsUrl($name);
        return $this->readVersion($url);
    }

    /**
     * @param $ext
     * @return string
     */
    protected function getVersionsUrl($ext)
    {
        $baseUrl = 'http://posim.com/webext/magento/magento2/';
        return $baseUrl . strtolower(str_replace('POSIMWebExt_', '', $ext)) . '/versions.json';
    }

    /**
     * @param $ext
     * @return string
     */
    public function getExtFullName($ext)
    {
        switch ($ext) {
            case 'webextmanager':
                return 'POSIMWebExt_WebExtManager';
            case 'evolinkerapi':
                return 'POSIMWebExt_EVOLinkerAPI';
            case 'gclink':
                return 'POSIMWebExt_GCLink';
        }
    }

    /**
     * @param $ext
     * @param $version
     * @return bool
     */
    public function updateExtension($ext, $version)
    {
        $currentVersion = $this->getInstalledVersion($this->getExtFullName($ext));
        $newVersion = $version;

        if (version_compare($newVersion, $currentVersion, '<')) {
            $file = $ext . '-' . $currentVersion . '.zip';

        } else {
            //get version and install and return true
            $file = $ext . '-' . $version . '.zip';
        }
        $remoteZip = 'http://posim.com/downloads/MAGENTO/EXTENSIONS/MAGENTO2/' . $ext . '/' . $file;

        $localZip = $this->dir->getRoot() . '/' . $file;

        if ($openLocalZip = fopen($localZip, 'c')) {

            fclose($openLocalZip);
            chmod($localZip, 0777);

            if (fopen($remoteZip, 'r')) {
                $this->logger->debug('Attempting to save local ' . $file);
                file_put_contents($localZip, fopen($remoteZip, 'r'));

                $zip = new \ZipArchive;
                $zipOpen = $zip->open($localZip);

                if ($zipOpen === TRUE) {

                    $zip->extractTo($this->dir->getRoot() . '/');
                    $zip->close();
                    unlink($localZip);
                    $this->installer->testExecute();

                    /* milk was a bad choice */
                    $table = $this->resource->getTableName('setup_module');
                    $connection = $this->resource->getConnection('core_write');
                    $query = 'update ' . $table . ' set schema_version="' . $newVersion . '", data_version="' . $newVersion . '" where module="' . $this->getExtFullName($ext) . '"';
                    $connection->query($query);


                    $cacheTypes = array(
                        'config',
                        'layout',
                        'block_html',
                        'collections',
                        'reflection',
                        'db_ddl',
                        'eav',
                        'config_integration',
                        'config_integration_api',
                        'full_page',
                        'translate',
                        'config_webservice'
                    );
                    try {
                        foreach ($cacheTypes as $type) {
                            $this->cacheTypeList->cleanType($type);
                        }
                        foreach ($this->cachePool as $cache) {
                            $cache->getBackend()->clean();
                        }
                    } catch (\Exception $e) {
                        die('Error during Cache Clean: ' . $e->getMessage());
                    }

                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function addNotification($name)
    {
        $title = 'Update Available for ' . $name;
        $description = 'An update is available for the POSIM extension ' . $name . '. Please visit the Extension Manager page to complete the update. If you have any questions or need assistance with this update, please contact E-Commerce support at support@eposim.com.';
        $notification = $this->inbox->create();

        $notificationCollection = $notification->getCollection();
        foreach ($notificationCollection as $message) {
            if (strpos($message->getData()['title'], $name)) {
                $toDelete = $this->inbox->create();
                $toDelete->setId($message->getData()['notification_id'])->setIsRemove(1)->save();

            }
        }
        $notification->addCritical($title, $description, $this->adminHelper->getUrl('webextmanager/installed'));
    }


}