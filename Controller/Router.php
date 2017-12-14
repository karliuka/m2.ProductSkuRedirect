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
use Magento\Catalog\Model\ProductFactory;

/**
 * Product Sku Router
 */
class Router implements RouterInterface
{
    /**
     * Product Factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */	
	protected $_productFactory;
	
    /**
     * Action Factory
     *
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
     * Initialize Router
     *
     * @param ActionFactory $actionFactory
     * @param ResponseInterface $response
	 * @param ProductFactory $productFactory 	 
     */
    public function __construct(
        ActionFactory $actionFactory,
        ResponseInterface $response,
		ProductFactory $productFactory
    ) {
        $this->_actionFactory = $actionFactory;
        $this->_response = $response;
		$this->_productFactory = $productFactory;
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
		$productId = $this->_getIdBySku($identifier);
		if ($productId) {
			$this->_setRequestParam($request, $productId);
			return $this->_getActionForward($request);				
		}		
		return false;
    }
	
    /**
     * Initialize Request Param
     *
     * @param RequestInterface $request
     * @param integer $productId	 
     * @return void
     */
    protected function _setRequestParam($request, $productId)
    {
		$request
			->setModuleName('catalog')
			->setControllerName('product')
			->setActionName('view')
			->setParam('id', $productId);
    }
	
    /**
     * Retrieve Action Forward
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\Action\Forward
     */
    protected function _getActionForward($request)
    {
        return $this->_actionFactory->create(
            'Magento\Framework\App\Action\Forward',
            ['request' => $request]
        );
    }
	
    /**
     * Retrieve Product Id By Sku
     *
     * @param string $sku
     * @return integer
     */
    protected function _getIdBySku($sku)
    {
        return $this->_productFactory->create()
			->getIdBySku($sku);
    }	
}