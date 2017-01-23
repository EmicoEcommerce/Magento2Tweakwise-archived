<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\FilterList;

use Closure;
use Emico\Tweakwise\Exception\TweakwiseException;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\FilterList;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

class Plugin
{
    /**
     * @var ScopeConfigInterface
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $log;

    /**
     * @var Tweakwise
     */
    protected $tweakwiseFilterList;

    /**
     * @var bool
     */
    protected $tweakwiseExceptionThrown = false;

    /**
     * Proxy constructor.
     *
     * @param ScopeConfigInterface $config
     * @param LoggerInterface $logger
     * @param Tweakwise $tweakwiseFilterList
     */
    public function __construct(ScopeConfigInterface $config, LoggerInterface $logger, Tweakwise $tweakwiseFilterList)
    {
        $this->config = $config;
        $this->log = $logger;
        $this->tweakwiseFilterList = $tweakwiseFilterList;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        if ($this->tweakwiseExceptionThrown) {
            return false;
        }
        return (bool) $this->config->getValue('tweakwise/layered/enabled');
    }

    /**
     * @param FilterList $subject
     * @param Closure $proceed
     * @param Layer $layer
     * @return AbstractFilter[]
     */
    public function aroundGetFilters(FilterList $subject, Closure $proceed, Layer $layer)
    {
        if (!$this->isEnabled()) {
            return $proceed($layer);
        }

        try {
            return $this->tweakwiseFilterList->getFilters($layer);
        } catch (TweakwiseException $e) {
            $this->log->critical($e);
            $this->tweakwiseExceptionThrown = true;

            return $proceed($layer);
        }
    }
}
