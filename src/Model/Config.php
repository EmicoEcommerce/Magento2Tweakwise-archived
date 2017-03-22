<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\Store;

class Config
{
    /**
     * @var ScopeConfigInterface
     */
    protected $config;

    /**
     * @var bool
     */
    protected $tweakwiseExceptionThrown = false;

    /**
     * Export constructor.
     *
     * @param ScopeConfigInterface $config
     */
    public function __construct(ScopeConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @param bool $thrown
     * @return $this
     */
    public function setTweakwiseExceptionThrown($thrown = true)
    {
        $this->tweakwiseExceptionThrown = (bool) $thrown;
        return $this;
    }

    /**
     * @param Store|null $store
     * @return string
     */
    public function getGeneralServerUrl(Store $store = null)
    {
        return (string) $this->getStoreConfig($store, 'tweakwise/general/server_url');
    }

    /**
     * @param Store|null $store
     * @return string
     */
    public function getGeneralAuthenticationKey(Store $store = null)
    {
        return (string) $this->getStoreConfig($store, 'tweakwise/general/authentication_key');
    }

    /**
     * @param Store|null $store
     * @return float
     */
    public function getTimeout(Store $store = null)
    {
        return (float) $this->getStoreConfig($store, 'tweakwise/general/timeout');
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function isLayeredEnabled(Store $store = null)
    {
        if ($this->tweakwiseExceptionThrown) {
            return false;
        }

        if ($store) {
            return $store->getConfig('tweakwise/layered/enabled');
        }

        return (bool) $this->getStoreConfig($store, 'tweakwise/layered/enabled');
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function getCategoryAsLink(Store $store = null)
    {
        return (bool) $this->getStoreConfig($store, 'tweakwise/layered/category_links');
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function getHideSingleOptions(Store $store = null)
    {
        return (bool) $this->getStoreConfig($store, 'tweakwise/layered/hide_single_option');
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function getUseDefaultLinkRenderer(Store $store = null)
    {
        return (bool) $this->getStoreConfig($store, 'tweakwise/layered/default_link_renderer');
    }

    /**
     * @param Store|null $store
     * @param string $path
     * @return mixed|null|string
     */
    protected function getStoreConfig(Store $store = null, $path)
    {
        if ($store) {
            return $store->getConfig($path);
        }

        return $this->config->getValue($path);
    }
}