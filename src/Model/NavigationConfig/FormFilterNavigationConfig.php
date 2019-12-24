<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */
declare(strict_types=1);


namespace Emico\Tweakwise\Model\NavigationConfig;


use Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\SliderRenderer;

class FormFilterNavigationConfig implements NavigationConfigInterface
{
    /**
     * @inheritDoc
     */
    public function getJsFilterNavigationConfig(bool $hasAlternateSortOrder = false)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getJsFormConfig()
    {
        return  [
            'tweakwiseNavigationForm' => []
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
