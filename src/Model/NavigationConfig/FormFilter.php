<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */

namespace Emico\Tweakwise\Model\NavigationConfig;

use Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\SliderRenderer;

class FormFilter implements NavigationConfigInterface
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
        return  [
            'tweakwiseNavigationFilterForm' => []
        ];
    }

    /**
     * @inheritDoc
     */
    public function getJsSliderConfig(SliderRenderer $sliderRenderer)
    {
        return [
            'tweakwiseNavigationSlider' => [
                'formFilters' => true,
            ]
        ];
    }
}
