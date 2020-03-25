<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */

namespace Emico\Tweakwise\Controller\Ajax;

use Emico\Tweakwise\Model\AjaxNavigationResult;
use Emico\Tweakwise\Model\Config;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Layer\Resolver;

/**
 * Class Navigation
 * Handles ajax filtering requests for category pages
 * @package Emico\Tweakwise\Controller\Ajax
 */
class Navigation extends Action
{
    /**
     * @var Config
     */
    protected $config;


    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var AjaxNavigationResult
     */
    protected $ajaxNavigationResult;

    /**
     * @var Resolver
     */
    protected $layerResolver;

    /**
     * @var string[]
     */
    protected $layerMap;

    /**
     * @var string[]
     */
    protected $layoutMap;

    /**
     * Navigation constructor.
     * @param Context $context Request context
     * @param Config $config Tweakwise configuration provider
     * @param Registry $registry
     * @param CategoryRepositoryInterface $categoryRepository
     * @param AjaxNavigationResult $ajaxNavigationResult
     * @param Resolver $layerResolver
     * @param string[] $layerMap
     * @param string[] $layoutMap
     */
    public function __construct(
        Context $context,
        Config $config,
        Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        AjaxNavigationResult $ajaxNavigationResult,
        Resolver $layerResolver,
        array $layerMap,
        array $layoutMap
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->registry = $registry;
        $this->categoryRepository = $categoryRepository;
        $this->ajaxNavigationResult = $ajaxNavigationResult;
        $this->layerResolver = $layerResolver;
        $this->layerMap = $layerMap;
        $this->layoutMap = $layoutMap;
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        if (!$this->config->isAjaxFilters()) {
            throw new NotFoundException(__('Page not found.'));
        }

        $type = $this->getRequest()->getParam('__tw_ajax_type');
        $this->initializeLayer($type);
        $categoryId = (int)$this->getRequest()->getParam('__tw_object_id') ?: 2;

        // Register the category, its needed while rendering filters and products
        if (!$this->registry->registry('current_category')) {
            $category = $this->categoryRepository->get($categoryId);
            $this->registry->register('current_category', $category);
        }

        $this->initializeLayout($type);
        return $this->ajaxNavigationResult;
    }

    /**
     * @param string $type
     */
    protected function initializeLayer(string $type)
    {
        $layerType = $this->layerMap[$type] ?: 'category';
        $this->layerResolver->create($layerType);
    }

    /**
     * @param $type
     */
    protected function initializeLayout($type)
    {
        $handle = $this->layoutMap[$type];
        $this->ajaxNavigationResult->addHandle($handle);
    }
}
