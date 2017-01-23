<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\FilterList;

use Emico\Tweakwise\Exception\TweakwiseException;
use Magento\Catalog\Model\Layer\FilterableAttributeListInterface;
use Magento\Catalog\Model\Layer\FilterList;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Psr\Log\LoggerInterface;

class EnabledSwitch
{
    /**
     * @var ScopeConfigInterface
     */
    protected $config;

    /**
     * @varObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var FilterList
     */
    protected $originalInstance;

    /**
     * @var Tweakwise
     */
    protected $tweakwiseInstance;

    /**
     * @var LoggerInterface
     */
    protected $log;

    /**
     * @var bool
     */
    protected $tweakwiseExceptionThrown = false;

    /**
     * @var FilterableAttributeListInterface
     */
    protected $filterableAttributes;

    /**
     * @var array
     */
    protected $filters;

    /**
     * Proxy constructor.
     *
     * @param ScopeConfigInterface $config
     * @param ObjectManagerInterface $objectManager
     * @param LoggerInterface $logger
     * @param FilterableAttributeListInterface $filterableAttributes
     * @param array $filters
     */
    public function __construct(ScopeConfigInterface $config, ObjectManagerInterface $objectManager, LoggerInterface $logger,
                                FilterableAttributeListInterface $filterableAttributes, array $filters = [])
    {
        $this->config = $config;
        $this->objectManager = $objectManager;
        $this->log = $logger;
        $this->filterableAttributes = $filterableAttributes;
        $this->filters = $filters;
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
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, array $arguments = [])
    {
        if ($this->isEnabled()) {
            try {
                return $this->callTweakwise($name, $arguments);
            } catch (TweakwiseException $e) {
                $this->log->critical($e);
                $this->tweakwiseExceptionThrown = true;

                return $this->callOriginal($name, $arguments);
            }
        } else {
            return $this->callOriginal($name, $arguments);
        }
    }

    /**
     * @return array
     */
    private function getInitializeParameters()
    {
        return [
            'filterableAttributes' => $this->filterableAttributes,
            'filters' => $this->filters,
        ];
    }

    /**
     * @return Tweakwise
     */
    private function getTweakwiseInstance()
    {
        if (!$this->tweakwiseInstance) {
            $this->tweakwiseInstance = $this->objectManager->create(Tweakwise::class, $this->getInitializeParameters());
        }
        return $this->tweakwiseInstance;
    }

    /**
     * @return object
     */
    private function getOriginalInstance()
    {
        if (!$this->originalInstance) {
            $this->originalInstance = $this->objectManager->create(FilterList::class, $this->getInitializeParameters());
        }
        return $this->originalInstance;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    private function callTweakwise($name, array $arguments = [])
    {
        $type = $this->getTweakwiseInstance();
        return call_user_func_array([$type, $name], $arguments);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    private function callOriginal($name, array $arguments = [])
    {
        $type = $this->getOriginalInstance();
        return call_user_func_array([$type, $name], $arguments);
    }

}
