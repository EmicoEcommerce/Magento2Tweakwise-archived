<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\Url\UrlModel;

use Emico\Tweakwise\Model\Config;
use Emico\Tweakwise\Model\Config\Source\QueryFilterType;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Route\ConfigInterface;
use Magento\Framework\Session\Generic;
use Magento\Framework\Session\SidResolverInterface;
use Magento\Framework\Url as MagentoUrl;
use Magento\Framework\Url\HostChecker;
use Magento\Framework\Url\QueryParamsResolverInterface;
use Magento\Framework\Url\RouteParamsPreprocessorInterface;
use Magento\Framework\Url\RouteParamsResolverFactory;
use Magento\Framework\Url\ScopeResolverInterface;
use Magento\Framework\Url\SecurityInfoInterface;
use Magento\Framework\Url\ModifierInterface;
use Magento\Framework\Url\ParamEncoder;

class V21 extends MagentoUrl
{
    /**
     * @var Config
     */
    private $config;

    /**
     * UrlModel constructor.
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
     * @param array $data
     * @param HostChecker|null $hostChecker
     * @param ModifierInterface|null $urlModifier
     * @param ParamEncoder|null $paramEncoder
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
        string $scopeType,
        Config $config,
        array $data = [],
        HostChecker $hostChecker = null,
        ModifierInterface $urlModifier = null,
        ParamEncoder $paramEncoder = null
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
            $urlModifier,
            $paramEncoder
        );

        $this->config = $config;
    }

    /**
     * Return query string with filtered params
     *
     * @param bool $escape
     * @return string
     */
    protected function _getQuery($escape = false)
    {
        if ($this->config->getQueryFilterType() === QueryFilterType::TYPE_NONE) {
            return parent::_getQuery($escape);
        }

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
    private function shouldFilter($param)
    {
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