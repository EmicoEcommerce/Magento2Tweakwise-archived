<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */

namespace Emico\Tweakwise\Controller\Ajax;

use Emico\Tweakwise\Model\AjaxNavigationResult;
use Emico\Tweakwise\Model\Config;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;

/**
 * Class Search
 * Handles ajax filtering requests for the search result pages
 * @package Emico\Tweakwise\Controller\Ajax
 */
class Search extends Action
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var AjaxNavigationResult
     */
    protected $ajaxNavigationResult;

    /**
     * @var Resolver
     */
    private $layerResolver;

    /**
     * Navigation constructor.
     * @param Context $context Request context
     * @param Config $config Tweakwise configuration provider
     * @param Resolver $layerResolver
     * @param AjaxNavigationResult $ajaxNavigationResult
     */
    public function __construct(
        Context $context,
        Config $config,
        Resolver $layerResolver,
        AjaxNavigationResult $ajaxNavigationResult
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->layerResolver = $layerResolver;
        $this->ajaxNavigationResult = $ajaxNavigationResult;
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     */
    public function execute()
    {
        if (!$this->config->isAjaxFilters()) {
            throw new NotFoundException(__('Page not found.'));
        }

        $this->layerResolver->create(Resolver::CATALOG_LAYER_SEARCH);
        return $this->ajaxNavigationResult;
    }
}
