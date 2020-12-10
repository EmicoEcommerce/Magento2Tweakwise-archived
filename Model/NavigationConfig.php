<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */

namespace Emico\Tweakwise\Model;

use Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\SliderRenderer;
use Emico\Tweakwise\Model\Catalog\Layer\NavigationContext\CurrentContext;
use Emico\Tweakwise\Model\FilterFormInputProvider\FilterFormInputProviderInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class NavigationConfig
 * This class provides configuration for the various data-mage-init statements in phtml files.
 * It will be used by
 * @see \Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\DefaultRenderer
 * @see \Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\SwatchRenderer
 * @see \Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\SliderRenderer
 * @package Emico\Tweakwise\Model
 */
class NavigationConfig implements ArgumentInterface, FilterFormInputProviderInterface
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
     * @var CurrentContext
     */
    protected $currentNavigationContext;

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var FilterFormInputProviderInterface
     */
    protected $filterFormInputProvider;

    /**
     * NavigationConfig constructor.
     * @param Config $config
     * @param UrlInterface $url
     * @param CurrentContext $currentNavigationContext
     * @param ProductMetadataInterface $productMetadata
     * @param FilterFormInputProviderInterface $filterFormInputProvider
     * @param Json $jsonSerializer
     */
    public function __construct(
        Config $config,
        UrlInterface $url,
        CurrentContext $currentNavigationContext,
        ProductMetadataInterface $productMetadata,
        FilterFormInputProviderInterface $filterFormInputProvider,
        Json $jsonSerializer
    ) {
        $this->config = $config;
        $this->jsonSerializer = $jsonSerializer;
        $this->url = $url;
        $this->currentNavigationContext = $currentNavigationContext;
        $this->productMetadata = $productMetadata;
        $this->filterFormInputProvider = $filterFormInputProvider;
    }

    /**
     * @inheritDoc
     */
    public function getFilterFormInput(): array
    {
        return $this->filterFormInputProvider->getFilterFormInput();
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
     * @param bool $hasAlternateSort
     */
    public function getJsSortConfig($hasAlternateSort = null)
    {
        return $this->jsonSerializer->serialize(
            [
                'tweakwiseNavigationSort' => [
                    'hasAlternateSort' => (bool)$hasAlternateSort
                ]
            ]
        );
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
        return $this->url->getUrl('tweakwise/ajax/navigation');
    }
}
