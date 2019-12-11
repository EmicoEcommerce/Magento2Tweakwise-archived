<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2018.
 */

namespace Emico\Tweakwise\Block\LayeredNavigation\Navigation;

use Emico\Tweakwise\Model\NavigationConfig;
use Magento\LayeredNavigation\Block\Navigation;

class Plugin
{
    /**
     * @var NavigationConfig
     */
    protected $navigationConfig;

    /**
     * @param NavigationConfig $navigationConfig
     */
    public function __construct(NavigationConfig $navigationConfig)
    {
        $this->navigationConfig = $navigationConfig;
    }

    /**
     * @param Navigation $block
     * @param $result
     * @return mixed
     */
    public function afterGetFilters(Navigation $block, $result)
    {
        $block->setData('form_filters', $this->navigationConfig->getJsFormConfig());
        return $result;
    }
}
