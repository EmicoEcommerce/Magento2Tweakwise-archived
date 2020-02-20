<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2019 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\Url;

use Emico\Tweakwise\Model\Config;
use Emico\Tweakwise\Model\Config\Source\QueryFilterType;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Route\ConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Session\Generic;
use Magento\Framework\Session\SidResolverInterface;
use Magento\Framework\Url as MagentoUrl;
use Magento\Framework\Url\HostChecker;
use Magento\Framework\Url\QueryParamsResolverInterface;
use Magento\Framework\Url\RouteParamsPreprocessorInterface;
use Magento\Framework\Url\RouteParamsResolverFactory;
use Magento\Framework\Url\ScopeResolverInterface;
use Magento\Framework\Url\SecurityInfoInterface;

class UrlModel extends MagentoUrl
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var array
     */
    protected $tweakwiseSystemParams;

    /**
     * @param ConfigInterface $routeConfig
     * @param RequestInterface $request
     * @param SecurityInfoInterface $urlSecurityInfo
     * @param ScopeResolverInterface $scopeResolver
     * @param Generic $session
     * @param SidResolverInterface $sidResolver
     * @param RouteParamsResolverFactory $routeParamsResolverFactory
     * @param QueryParamsResolverInterface $queryParamsResolver
     * @param ScopeConfigInterface $scopeConfig
     * @param RouteParamsPreprocessorInterface $routeParamsPreprocessor
     * @param string $scopeType
     * @param Config $config
     * @param array $tweakwiseSystemParams
     * @param array $data
     * @param HostChecker|null $hostChecker
     * @param Json|null $serializer
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ConfigInterface $routeConfig,
        RequestInterface $request,
        SecurityInfoInterface $urlSecurityInfo,
        ScopeResolverInterface $scopeResolver,
        Generic $session,
        SidResolverInterface $sidResolver,
        RouteParamsResolverFactory $routeParamsResolverFactory,
        QueryParamsResolverInterface $queryParamsResolver,
        ScopeConfigInterface $scopeConfig,
        RouteParamsPreprocessorInterface $routeParamsPreprocessor,
        $scopeType,
        Config $config,
        array $tweakwiseSystemParams = [],
        array $data = [],
        HostChecker $hostChecker = null,
        Json $serializer = null
    ) {
        parent::__construct(
            $routeConfig,
            $request,
            $urlSecurityInfo,
            $scopeResolver,
            $session,
            $sidResolver,
            $routeParamsResolverFactory,
            $queryParamsResolver,
            $scopeConfig,
            $routeParamsPreprocessor,
            $scopeType,
            $data,
            $hostChecker,
            $serializer
        );

        $this->config = $config;
        $this->tweakwiseSystemParams = $tweakwiseSystemParams;
    }

    /**
     * Return query string with filtered params
     *
     * @param bool $escape
     * @return string
     */
    protected function _getQuery($escape = false)
    {
        $newParams = [];
        foreach ($this->_queryParamsResolver->getQueryParams() as $param => $value) {
            if ($this->shouldFilter($param)) {
                continue;
            }

            $newParams[$param] = $value;
        }
        $this->_queryParamsResolver->setQueryParams($newParams);

        return parent::_getQuery($escape);
    }

    /**
     * @param string $param
     * @return bool
     */
    private function shouldFilter($param): bool
    {
        // First check for our system parameters, which need to be filtered regardless of settings.
        if (in_array($param, $this->tweakwiseSystemParams, true)) {
            return true;
        }

        $filterType = $this->config->getQueryFilterType();
        if ($filterType === QueryFilterType::TYPE_NONE) {
            return false;
        }

        if ($filterType === QueryFilterType::TYPE_REGEX) {
            return (bool) preg_match('/' . $this->config->getQueryFilterRegex() . '/', $param);
        }

        if ($filterType === QueryFilterType::TYPE_SPECIFIC) {
            return \in_array($param, $this->config->getQueryFilterArguments(), true);
        }

        return true;
    }
}
