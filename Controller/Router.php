<?php
/**
 * Copyright © 2011-2018 Karliuka Vitalii(karliuka.vitalii@gmail.com)
 *
 * See COPYING.txt for license details.
 */
namespace Faonni\ProductSkuRedirect\Controller;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\ProductFactory;

/**
 * Product Sku Router
 */
class Router implements RouterInterface
{
    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * Initialize router
     *
     * @param ActionFactory $actionFactory
     * @param ProductFactory $productFactory
     */
    public function __construct(
        ActionFactory $actionFactory,
        ProductFactory $productFactory
    ) {
        $this->actionFactory = $actionFactory;
        $this->productFactory = $productFactory;
    }

    /**
     * Validate and match
     *
     * @param RequestInterface $request
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        $productId = $this->getIdBySku($identifier);
        if ($productId) {
            $this->setRequestParam($request, $productId);
            return $this->getActionForward($request);
        }

        return null;
    }

    /**
     * Initialize request param
     *
     * @param RequestInterface $request
     * @param integer $productId
     * @return void
     */
    private function setRequestParam($request, $productId)
    {
        $request
            ->setModuleName('catalog')
            ->setControllerName('product')
            ->setActionName('view')
            ->setParam('id', $productId);
    }

    /**
     * Retrieve action forward
     *
     * @param RequestInterface $request
     * @return ActionInterface
     */
    private function getActionForward($request)
    {
        /** @var Forward $action */
        $action = $this->actionFactory->create(Forward::class);
        $action->dispatch($request);

        return $action;
    }

    /**
     * Retrieve product id by sku
     *
     * @param string $sku
     * @return integer
     */
    private function getIdBySku($sku)
    {
        return $this->productFactory->create()
            ->getIdBySku($sku);
    }
}
