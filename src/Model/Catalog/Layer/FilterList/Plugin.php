<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\FilterList;

use Closure;
use Emico\Tweakwise\Model\Config;
use Emico\Tweakwise\Exception\TweakwiseException;
use Emico\TweakwiseExport\Model\Logger;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\FilterList;

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
     * @var Tweakwise
     */
    protected $tweakwiseFilterList;

    /**
     * Proxy constructor.
     *
     * @param Config $config
     * @param Logger $log
     * @param Tweakwise $tweakwiseFilterList
     */
    public function __construct(Config $config, Logger $log, Tweakwise $tweakwiseFilterList)
    {
        $this->config = $config;
        $this->log = $log;
        $this->tweakwiseFilterList = $tweakwiseFilterList;
    }

    /**
     * @param FilterList $subject
     * @param Closure $proceed
     * @param Layer $layer
     * @return AbstractFilter[]
     */
    public function aroundGetFilters(FilterList $subject, Closure $proceed, Layer $layer)
    {
        if (!$this->config->isLayeredEnabled()) {
            return $proceed($layer);
        }

        try {
            return $this->tweakwiseFilterList->getFilters($layer);
        } catch (TweakwiseException $e) {
            $this->log->critical($e);
            $this->config->setTweakwiseExceptionThrown();

            return $proceed($layer);
        }
    }
}
