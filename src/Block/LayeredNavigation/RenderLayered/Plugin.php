<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Block\LayeredNavigation\RenderLayered;

use Closure;
use Emico\Tweakwise\Exception\TweakwiseException;
use Emico\Tweakwise\Model\Catalog\Layer\Filter;
use Emico\Tweakwise\Model\Config;
use Emico\TweakwiseExport\Model\Logger;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Swatches\Block\LayeredNavigation\RenderLayered;

class Plugin
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Logger
     */
    protected $log;

    /**
     * @var AbstractFilter
     */
    protected $filter;

    /**
     * Proxy constructor.
     *
     * @param Config $config
     * @param Logger $log
     */
    public function __construct(Config $config, Logger $log)
    {
        $this->config = $config;
        $this->log = $log;
    }

    /**
     * @param RenderLayered $subject
     * @param Closure $proceed
     * @param AbstractFilter $filter
     * @return $this
     */
    public function aroundSetSwatchFilter(RenderLayered $subject, Closure $proceed, AbstractFilter $filter)
    {
        $this->filter = $filter;
        return $proceed($filter);
    }

    /**
     * @param RenderLayered $subject
     * @param Closure $proceed
     * @param string $attributeCode
     * @param int $optionId
     * @return string
     */
    public function aroundBuildUrl(RenderLayered $subject, Closure $proceed, $attributeCode, $optionId)
    {
        if (!$this->config->isLayeredEnabled()) {
            return $proceed($attributeCode, $optionId);
        }

        $filter = $this->filter;
        if (!$filter instanceof Filter) {
            return $proceed($attributeCode, $optionId);
        }

        $item = $filter->getItemByOptionId($optionId);
        if (!$item) {
            return $proceed($attributeCode, $optionId);
        }

        try {
            return $item->getUrl();
        } catch (TweakwiseException $e) {
            $this->log->critical($e);
            $this->config->setTweakwiseExceptionThrown();

            return $proceed($attributeCode, $optionId);
        }
    }
}