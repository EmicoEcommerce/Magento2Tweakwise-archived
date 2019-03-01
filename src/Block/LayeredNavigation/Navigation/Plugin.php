<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2018.
 */

namespace Emico\Tweakwise\Block\LayeredNavigation\Navigation;

use Emico\Tweakwise\Model\Config;
use Magento\LayeredNavigation\Block\Navigation;

class Plugin
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param Navigation $block
     * @param $result
     * @return mixed
     */
    public function afterGetFilters(Navigation $block, $result)
    {
        $block->setData('form_filters', $this->config->getUseFormFilters());
        return $result;
    }
}