<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */
declare(strict_types=1);


namespace Emico\Tweakwise\Model\NavigationConfig;

use Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\SliderRenderer;

/**
 * Class AjaxFormFilterNavigationConfig
 * @package Emico\Tweakwise\Model\NavigationConfig
 */
class AjaxFormFilterNavigationConfig implements NavigationConfigInterface
{
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
     * @inheritDoc
     */
    public function getJsFormConfig()
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getJsSliderConfig(SliderRenderer $sliderRenderer)
    {
        return [
            'tweakwiseNavigationSlider' => [
                'ajaxFilters' => true,
                'formFilters' => true,
            ]
        ];
    }
}
