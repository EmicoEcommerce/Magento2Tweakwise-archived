<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */
declare(strict_types=1);

namespace Emico\Tweakwise\Model\NavigationConfig;

use Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\SliderRenderer;
use Emico\Tweakwise\Model\Config;
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
class NavigationConfig implements NavigationConfigInterface, ArgumentInterface
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
     * @var NavigationConfigInterface[]
     */
    protected $navigationConfigProviders;

    /**
     * @var NavigationConfigInterface
     */
    protected $providerInstance;

    /**
     * NavigationConfig constructor.
     * @param Config $config
     * @param Json $jsonSerializer
     * @param NavigationConfigInterface[] $navigationConfigProviders
     */
    public function __construct(
        Config $config,
        Json $jsonSerializer,
        array $navigationConfigProviders
    ) {
        $this->config = $config;
        $this->jsonSerializer = $jsonSerializer;
        $this->navigationConfigProviders = $navigationConfigProviders;
    }

    /**
     * @return NavigationConfigInterface
     */
    protected function getInstance(): NavigationConfigInterface
    {
        if ($this->providerInstance) {
            return $this->providerInstance;
        }

        $this->providerInstance = $this->getProviderInstance();

        return $this->providerInstance;
    }

    /**
     * @return NavigationConfigInterface
     * @TODO move to factory method? If so cleanup di.xml
     */
    protected function getProviderInstance(): NavigationConfigInterface
    {
        $ajaxEnabled = $this->config->isAjaxFiltering();
        $formFiltersEnabled = $this->config->getUseFormFilters();

        if ($ajaxEnabled && $formFiltersEnabled) {
            return $this->navigationConfigProviders['ajax_form_filter'];
        }

        if ($ajaxEnabled) {
            return $this->navigationConfigProviders['ajax'];
        }

        if ($formFiltersEnabled) {
            return $this->navigationConfigProviders['form_filter'];
        }

        return $this->navigationConfigProviders['default'];
    }

    /**
     * @param bool $hasAlternateSortOrder
     * @return string
     */
    public function getJsFilterNavigationConfig()
    {
        $jsFilterNavigationConfig = $this->getInstance()
            ->getJsFilterNavigationConfig();

        return $jsFilterNavigationConfig ? $this->jsonSerializer->serialize($jsFilterNavigationConfig) : '';
    }

    /**
     * @return string
     */
    public function getJsFormConfig()
    {
        $jsFormConfig = $this->getInstance()->getJsFormConfig();
        return $jsFormConfig ? $this->jsonSerializer->serialize($jsFormConfig) : '';
    }

    /**
     * @param SliderRenderer $sliderRenderer
     * @return string
     */
    public function getJsSliderConfig(SliderRenderer $sliderRenderer)
    {
        $jsSliderConfig = $this->getInstance()->getJsSliderConfig($sliderRenderer) ?: [];
        $jsSliderConfig = array_merge_recursive($this->getDefaultSliderConfig($sliderRenderer), $jsSliderConfig);

        return $jsSliderConfig ? $this->jsonSerializer->serialize($jsSliderConfig) : '';
    }

    /**
     * @param SliderRenderer $sliderRenderer
     * @return array
     */
    protected function getDefaultSliderConfig(SliderRenderer $sliderRenderer)
    {
        return [
            'tweakwiseNavigationSlider' => [
                'filterUrl' => $sliderRenderer->getFilterUrl(),
                'prefix' => "<span class=\"prefix\">{$sliderRenderer->getItemPrefix()}</span>",
                'postfix' => "<span class=\"prefix\">{$sliderRenderer->getItemPostfix()}</span>",
                'container' => "#attribute-slider-{$sliderRenderer->getCssId()}",
                'min' => $sliderRenderer->getMinValue(),
                'max' => $sliderRenderer->getMaxValue(),
                'currentMin' => $sliderRenderer->getCurrentMinValue(),
                'currentMax' => $sliderRenderer->getCurrentMaxValue(),
            ]
        ];
    }

    /**
     * @return bool
     */
    public function getUseFormFilters()
    {
        return $this->config->getUseFormFilters();
    }

    /**
     * @return bool
     */
    public function isAjaxNavigation()
    {
        return $this->config->isAjaxFiltering();
    }
}
