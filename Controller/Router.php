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
 * Product Sku Router
 */
class Router implements RouterInterface
{
    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	
    /**
     * Action Factory
     *
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $_actionFactory;
	
    /**
     * Response Interface
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;
	
    /**
     * Initialize Router
     *
     * @param ActionFactory $actionFactory
     * @param ResponseInterface $response
	 * @param ObjectManagerInterface $objectManager 	 
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
     * Validate And Match
     *
     * @param RequestInterface $request
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