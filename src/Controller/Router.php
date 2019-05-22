<?php
/**
 * @author Bram Gerritsen <bgerritsen@emico.nl>
 * @copyright (c) Emico B.V. 2017
 */

namespace Emico\Tweakwise\Controller;

use Emico\Tweakwise\Model\Catalog\Layer\Url\RouteMatchingInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy\UrlStrategyFactory;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\Request\Http as MagentoHttpRequest;

class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * @var RouteMatchingInterface
     */
    private $routeMatchingStrategy;

    /**
     * Router constructor.
     * @param ActionFactory $actionFactory
     * @param UrlStrategyFactory $urlStrategyFactory
     */
    public function __construct(ActionFactory $actionFactory, UrlStrategyFactory $urlStrategyFactory)
    {
        $this->actionFactory = $actionFactory;
        $this->routeMatchingStrategy = $urlStrategyFactory->create(RouteMatchingInterface::class);
    }

    /**
     * Match application action by request
     *
     * @param RequestInterface $request
     * @return bool|ActionInterface
     */
    public function match(RequestInterface $request)
    {
        if (!$request instanceof MagentoHttpRequest) {
            return false;
        }

        $result = $this->routeMatchingStrategy->match($request);

        if ($result === false) {
            return false;
        }

        if ($result instanceof ActionInterface) {
            return $result;
        }

        return $this->actionFactory->create(
            Forward::class,
            ['request' => $request]
        );
    }
}