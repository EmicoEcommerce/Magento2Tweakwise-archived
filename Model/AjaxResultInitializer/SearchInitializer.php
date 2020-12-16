<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\AjaxResultInitializer;

use Emico\Tweakwise\Model\AjaxNavigationResult;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\App\RequestInterface;

class SearchInitializer implements InitializerInterface
{
    /**
     * @var Resolver
     */
    protected $layerResolver;

    /**
     * AjaxResultSearchInitializer constructor.
     * @param Resolver $layerResolver
     */
    public function __construct(Resolver $layerResolver)
    {
        $this->layerResolver = $layerResolver;
    }

    /**
     * @inheritDoc
     */
    public function initializeAjaxResult(AjaxNavigationResult $ajaxNavigationResult, RequestInterface $request)
    {
        $this->initializeLayer();
        $this->initializeLayout($ajaxNavigationResult);
    }

    /**
     * @param AjaxNavigationResult $ajaxNavigationResult
     */
    protected function initializeLayout(AjaxNavigationResult $ajaxNavigationResult)
    {
        $ajaxNavigationResult->addHandle(self::LAYOUT_HANDLE_SEARCH);
    }

    /**
     * Create search Layer
     */
    protected function initializeLayer()
    {
        $this->layerResolver->create(Resolver::CATALOG_LAYER_SEARCH);
    }
}
