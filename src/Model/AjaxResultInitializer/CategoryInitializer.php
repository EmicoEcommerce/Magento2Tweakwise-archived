<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\AjaxResultInitializer;

use Emico\Tweakwise\Model\AjaxNavigationResult;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;

class CategoryInitializer implements InitializerInterface
{
    /**
     * @var Resolver
     */
    protected $layerResolver;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * AjaxResultCategoryInitializer constructor.
     * @param Resolver $layerResolver
     * @param Registry $registry
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        Resolver $layerResolver,
        Registry $registry,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->layerResolver = $layerResolver;
        $this->registry = $registry;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     */
    public function initializeAjaxResult(
        AjaxNavigationResult $ajaxNavigationResult,
        RequestInterface $request
    ) {
        $this->initializeLayer();
        $this->initializeLayout($ajaxNavigationResult);
        $this->initializeRegistry($request);
    }

    /**
     * @param AjaxNavigationResult $ajaxNavigationResult
     */
    protected function initializeLayout(AjaxNavigationResult $ajaxNavigationResult)
    {
        $ajaxNavigationResult->addHandle(self::LAYOUT_HANDLE_CATEGORY);
    }

    /**
     * Create category layer
     */
    protected function initializeLayer()
    {
        $this->layerResolver->create(Resolver::CATALOG_LAYER_CATEGORY);
    }

    /**
     * @param RequestInterface $request
     * @throws NoSuchEntityException
     */
    private function initializeRegistry(RequestInterface $request)
    {
        // Register the category, its needed while rendering filters and products
        if (!$this->registry->registry('current_category')) {
            $categoryId = (int)$request->getParam('__tw_object_id') ?: 2;
            $category = $this->categoryRepository->get($categoryId);
            $this->registry->register('current_category', $category);
        }
    }
}
