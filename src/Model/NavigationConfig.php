<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */

namespace Emico\Tweakwise\Model;

use Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\SliderRenderer;
use Emico\Tweakwise\Model\Catalog\Layer\NavigationContext\CurrentContext;
use Emico\Tweakwise\Model\Client\Request\ProductSearchRequest;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterfaceFactory;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class NavigationConfig
 * This class provides configuration for the various data-mage-init statements in phtml files.
 * It will be used by
 * @see \Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\DefaultRenderer
 * @see \Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\SwatchRenderer
 * @see \Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\SliderRenderer
 * @package Emico\Tweakwise\Model
 */
class NavigationConfig implements ArgumentInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Json
     */
    protected $jsonSerializer;

    /**
     * @var UrlInterface
     */
    protected $url;

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
    protected $categoryFactory;

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * NavigationConfig constructor.
     * @param Config $config
     * @param UrlInterface $url
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param CurrentContext $currentNavigationContext
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CategoryInterfaceFactory $categoryFactory
     * @param ProductMetadataInterface $productMetadata
     * @param Json $jsonSerializer
     */
    public function __construct(
        Config $config,
        UrlInterface $url,
        Registry $registry,
        StoreManagerInterface $storeManager,
        CurrentContext $currentNavigationContext,
        CategoryRepositoryInterface $categoryRepository,
        CategoryInterfaceFactory $categoryFactory,
        ProductMetadataInterface $productMetadata,
        Json $jsonSerializer
    ) {
        $this->config = $config;
        $this->jsonSerializer = $jsonSerializer;
        $this->url = $url;
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->currentNavigationContext = $currentNavigationContext;
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @return string
     */
    public function getJsFormConfig()
    {
        return $this->jsonSerializer->serialize(
            [
                'tweakwiseNavigationForm' => [
                    'formFilters' => $this->isFormFilters(),
                    'ajaxFilters' => $this->isAjaxFilters(),
                    'seoEnabled' => $this->config->isSeoEnabled(),
                    'ajaxEndpoint' => $this->getAjaxEndPoint(),
                    'filterSelector' => '#layered-filter-block',
                    'productListSelector' => '.products.wrapper',
                    'toolbarSelector' => '.toolbar.toolbar-products'
                ],
            ]
        );
    }

    /**
     * @param SliderRenderer $sliderRenderer
     * @return string
     */
    public function getJsSliderConfig(SliderRenderer $sliderRenderer)
    {
        $slider = $this->getSliderReference();
        return $this->jsonSerializer->serialize(
            [
                $slider => [
                    'ajaxFilters' => $this->isAjaxFilters(),
                    'formFilters' => $this->isFormFilters(),
                    'filterUrl' => $sliderRenderer->getFilterUrl(),
                    'prefix' => "<span class=\"prefix\">{$sliderRenderer->getItemPrefix()}</span>",
                    'postfix' => "<span class=\"postfix\">{$sliderRenderer->getItemPostfix()}</span>",
                    'container' => "#attribute-slider-{$sliderRenderer->getCssId()}",
                    'min' => $sliderRenderer->getMinValue(),
                    'max' => $sliderRenderer->getMaxValue(),
                    'currentMin' => $sliderRenderer->getCurrentMinValue(),
                    'currentMax' => $sliderRenderer->getCurrentMaxValue(),
                ]
            ]
        );
    }

    /**
     * Return which slider to use, the compat version has the full jquery/ui reference.
     * The normal slider definition has jquery-ui-modules/slider, which is only available from 2.3.3 and onwards
     *
     * @return string
     */
    protected function getSliderReference()
    {
        $mVersion = $this->productMetadata->getVersion();
        if (version_compare($mVersion, '2.3.3', '<')) {
            return 'tweakwiseNavigationSliderCompat';
        }

        return 'tweakwiseNavigationSlider';
    }

    /**
     * @return int|null
     */
    public function getCategoryId(): ?int
    {
        if ($this->isSearch()) {
            return null;
        }

        return (int)$this->getCategory()->getId() ?: null;
    }

    /**
     * Public because of plugin options
     *
     * @return string|null
     */
    public function getOriginalUrl(): ?string
    {
        return $this->isSearch()
            ? 'catalogsearch/result/index'
            : str_replace($this->url->getBaseUrl(), '', $this->getCategory()->getUrl());
    }

    /**
     * @return bool
     */
    public function isFormFilters()
    {
        return $this->config->isFormFilters();
    }

    /**
     * @return bool
     */
    public function isAjaxFilters()
    {
        return $this->config->isAjaxFilters();
    }

    /**
     * @return string
     */
    protected function getAjaxEndPoint()
    {
        if ($this->isSearch()) {
            return $this->url->getUrl('tweakwise/ajax/search');
        }

        return $this->url->getUrl('tweakwise/ajax/navigation');
    }

    /**
     * @return bool
     */
    public function isSearch()
    {
        return $this->currentNavigationContext->getRequest() instanceof ProductSearchRequest;
    }

    /**
     * @return string|null
     */
    public function getSearchTerm()
    {
        if (!$this->isSearch()) {
            return null;
        }

        return $this->currentNavigationContext->getRequest()->getParameter('tn_q');
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
}
