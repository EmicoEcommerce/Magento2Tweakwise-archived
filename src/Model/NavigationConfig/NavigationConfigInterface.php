<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */
declare(strict_types=1);


namespace Emico\Tweakwise\Model\NavigationConfig;


use Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\SliderRenderer;

interface NavigationConfigInterface
{
    /**
     * @return string|array
     */
    public function getJsFilterNavigationConfig();

    /**
     * @return string
     */
    public function getJsFormConfig();

    /**
     * @param SliderRenderer $sliderRenderer
     * @return string
     */
    public function getJsSliderConfig(SliderRenderer $sliderRenderer);
}
