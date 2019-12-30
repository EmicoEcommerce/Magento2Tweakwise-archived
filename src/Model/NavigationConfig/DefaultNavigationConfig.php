<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */
declare(strict_types=1);

namespace Emico\Tweakwise\Model\NavigationConfig;

use Emico\Tweakwise\Block\LayeredNavigation\RenderLayered\SliderRenderer;
use Emico\Tweakwise\Model\Config;

class DefaultNavigationConfig implements NavigationConfigInterface
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * DefaultNavigationConfig constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function getJsFilterNavigationConfig()
    {
        return [
            'tweakwiseNavigationSort' => [],
            'tweakwiseNavigationFilter' => [
                'seoEnabled' => $this->config->isSeoEnabled()
            ]
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
        return [];
    }
}
