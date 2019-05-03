<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_AffiliateSaveCart
 * @copyright  Copyright (c) 2018 Landofcoder (https://www.landofcoder.com/)
 * @license    https://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\AffiliateSaveCart\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Event manager
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $_affiliateSaveCartFactory;

    /**
     * @var bool
     */
    protected $dispatched;

    protected $_helper;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Framework\App\ActionFactory       $actionFactory   
     * @param \Magento\Framework\Event\ManagerInterface  $eventManager    
     * @param \Lof\AffiliateSaveCart\Model\AffiliateSaveCartFactory    $AffiliateSaveCartFactory     
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager    
     * @param \Lof\AffiliateSaveCart\Helper\Data               $data            
     * @param \Magento\Customer\Model\Session            $customerSession 
     * @param \Magento\Framework\Registry                $registry        
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Lof\AffiliateSaveCart\Model\AffiliateSaveCartFactory $AffiliateSaveCartFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Lof\AffiliateSaveCart\Helper\Data $data,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $registry
    ) {
        $this->actionFactory    = $actionFactory;
        $this->_eventManager    = $eventManager;
        $this->_saveCartFactory     = $AffiliateSaveCartFactory;
        $this->_storeManager    = $storeManager;
        $this->_helper          = $data;
        $this->_customerSession = $customerSession;
        $this->_coreRegistry    = $registry;
    }

    /**
     * Validate and Match Cms Page and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->dispatched) {
            $identifier = trim($request->getPathInfo(), '/');
            $origUrlKey = $identifier;

            $condition = new \Magento\Framework\DataObject(['identifier' => $identifier, 'continue' => true]);

            $identifier = $condition->getIdentifier();

            if ($condition->getRedirectUrl()) {
                $this->response->setRedirect($condition->getRedirectUrl());
                $request->setDispatched(true);
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Redirect',
                    ['request' => $request]
                    );
            }

            if (!$condition->getContinue()) {
                return null;
            }
            $enable = $this->_helper->getConfig('affiliatesavecart/enable');
            $route = $this->_helper->getConfig('affiliatesavecart/sharecart_route');
            if($route) {
                $route = trim($route);
                $route = str_replace(array("/"," "), "", $route);
            } else {
                $route = "";
            }
            if(!$enable || !$route) {
                $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                $this->dispatched = true;
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward',
                    ['request' => $request]
                    );
            }
            $identifiers = explode('/', $identifier);
            $cart_key = '';
            if(count($identifiers)==2){
                $identifier = $identifiers[0];
                $cart_key = $identifiers[1];
                $cart_key = str_replace(array(".html",".htm"), "", $cart_key);
            } else {
                $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                $this->dispatched = true;
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward',
                    ['request' => $request]
                    );
            }

            if(($identifier == $route) && $cart_key) {
                $cartId = (int)$cart_key;
                $savecart = $this->_saveCartFactory->create();
                $savecart->load($cartId);
                $this->dispatched = true;
                if($savecart && $savecart->getId()) {
                    if(!$this->_coreRegistry->registry("current_savecart")){
                        $this->_coreRegistry->register("current_savecart", $savecart);
                    }
                    $request->setModuleName('affiliatesavecart')
                    ->setControllerName('cart')
                    ->setActionName('addCart')
                    ->setParam('cart_id', $cartId);
                    $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);

                    return $this->actionFactory->create(
                        'Magento\Framework\App\Action\Forward',
                        ['request' => $request]
                        );
                }
            } else {
                $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                $this->dispatched = true;
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward',
                    ['request' => $request]
                    );
            }
        }
    }
}
