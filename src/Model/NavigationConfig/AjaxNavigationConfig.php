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
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

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
    private $currentNavigationContext;

    /**
     * AjaxNavigationConfig constructor.
     * @param Config $config
     * @param UrlInterface $url
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param CurrentContext $currentNavigationContext
     */
    public function __construct(
        Config $config,
        UrlInterface $url,
        Registry $registry,
        StoreManagerInterface $storeManager,
        CurrentContext $currentNavigationContext
    ) {
        $this->config = $config;
        $this->urlHelper = $url;
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->currentNavigationContext = $currentNavigationContext;
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
     * @return mixed
     */
    protected function getCategoryId()
    {
        if ($currentCategory = $this->registry->registry('current_category')) {
            return (int)$currentCategory->getId();
        }

        try {
            return (int)$this->storeManager->getStore()->getRootCategoryId();
        } catch (NoSuchEntityException $e) {
            return 2;
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

    /**
     * @return string
     */
    protected function getAjaxEndPoint()
    {
        if ($this->currentNavigationContext->getRequest() instanceof ProductSearchRequest) {
            return $this->urlHelper->getUrl('tweakwise/ajax/search');
        }

        return $this->urlHelper->getUrl('tweakwise/ajax/navigation');
    }
}
