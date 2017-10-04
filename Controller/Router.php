<?php
/**
 * Copyright © 2011-2017 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * 
 * See COPYING.txt for license details.
 */
namespace Faonni\ProductSkuRedirect\Controller;

use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * ProductSkuRedirect Router
 */
class Router implements RouterInterface
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $_actionFactory;
	
    /**
     * Response
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;
	
    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
	 * @param \Magento\Framework\ObjectManagerInterface $objectManager 
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)	 
     */
    public function __construct(
        ActionFactory $actionFactory,
        ResponseInterface $response,
		ObjectManagerInterface $objectManager
    ) {
        $this->_actionFactory = $actionFactory;
        $this->_response = $response;
		$this->_objectManager = $objectManager;
    }
	
    /**
     * Validate and Match
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');		
		$product = $this->_objectManager->get('Magento\Catalog\Model\Product')
			->loadByAttribute('sku', $identifier);
			
		if (!$product || !$product->getId()) {
			return false;
		}
		
		$request
			->setModuleName('catalog')
			->setControllerName('product')
			->setActionName('view')
			->setParam('id', $product->getId());
        /*
         * We have match and now we will forward action
         */
        return $this->_actionFactory->create(
            'Magento\Framework\App\Action\Forward',
            ['request' => $request]
        );
    }
}