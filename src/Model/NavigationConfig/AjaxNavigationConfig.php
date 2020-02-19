<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */
declare(strict_types=1);

namespace Emico\Tweakwise\Model\NavigationConfig;

use Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\SliderRenderer;
use Emico\Tweakwise\Model\Catalog\Layer\NavigationContext\CurrentContext;
use Emico\Tweakwise\Model\Client\Request\ProductSearchRequest;
use Emico\Tweakwise\Model\Config;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterfaceFactory;

class AjaxNavigationConfig implements NavigationConfigInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var UrlInterface
     */
    protected $urlHelper;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CurrentContext
     */
    protected $currentNavigationContext;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var CategoryInterfaceFactory
     */
    private $categoryFactory;

    /**
     * AjaxNavigationConfig constructor.
     * @param Config $config
     * @param UrlInterface $url
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param CurrentContext $currentNavigationContext
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CategoryInterfaceFactory $categoryFactory
     */
    public function __construct(
        Config $config,
        UrlInterface $url,
        Registry $registry,
        StoreManagerInterface $storeManager,
        CurrentContext $currentNavigationContext,
        CategoryRepositoryInterface $categoryRepository,
        CategoryInterfaceFactory $categoryFactory
    ) {
        $this->config = $config;
        $this->urlHelper = $url;
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->currentNavigationContext = $currentNavigationContext;
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @inheritDoc
     */
    public function getJsFilterNavigationConfig()
    {
        return [
            'tweakwiseNavigationSort' => [],
        ];
    }

    /**
     * @return int|null
     */
    protected function getCategoryId(): ?int
    {
        if ($this->isSearch()) {
            return null;
        }

        return ($this->getCategory()->getId()) ? (int)$this->getCategory()->getId() : null;
    }

    /**
     * Public because of plugin options
     *
     * @return string
     */
    public function getOriginalUrl(): ?string
    {
        return $this->isSearch()
            ? 'catalogsearch/result/index'
            : $this->getCategory()->getUrl();
    }

    /**
     * @return CategoryInterface|Category
     */
    protected function getCategory()
    {
        if ($currentCategory = $this->registry->registry('current_category')) {
            return $currentCategory;
        }

        try {
            $rootCategory = $this->storeManager->getStore()->getRootCategoryId();
        } catch (NoSuchEntityException $exception) {
            $rootCategory = 2;
        }

        try {
            return $this->categoryRepository->get($rootCategory);
        } catch (NoSuchEntityException $exception) {
            return $this->categoryFactory->create();
        }
    }

    /**
     * @inheritDoc
     */
    public function getJsFormConfig()
    {
        return [
            'tweakwiseNavigationFilterAjax' => [
                'seoEnabled' => $this->config->isSeoEnabled(),
                'originalUrl' => $this->getOriginalUrl(),
                'categoryId' => $this->getCategoryId(),
                'ajaxEndpoint' => $this->getAjaxEndPoint(),
                'filterSelector' => '#layered-filter-block',
                'productListSelector' => '.products.wrapper',
                'toolbarSelector' => '.toolbar.toolbar-products'
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getJsSliderConfig(SliderRenderer $sliderRenderer)
    {
        return [
            'tweakwiseNavigationSlider' => [
                'ajaxFilters' => true,
            ]
        ];
    }

    protected function isSearch()
    {
        return $this->currentNavigationContext->getRequest() instanceof ProductSearchRequest;
    }

    /**
     * @return string
     */
    protected function getAjaxEndPoint()
    {
        if ($this->isSearch()) {
            return $this->urlHelper->getUrl('tweakwise/ajax/search');
        }

        return $this->urlHelper->getUrl('tweakwise/ajax/navigation');
    }
}
