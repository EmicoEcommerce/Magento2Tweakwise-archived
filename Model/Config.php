<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model;

use Emico\Tweakwise\Exception\InvalidArgumentException;
use Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy\QueryParameterStrategy;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Serialize\Serializer\Json;

class Config
{
    /**
     * Recommendation types
     */
    public const RECOMMENDATION_TYPE_UPSELL = 'upsell';
    public const RECOMMENDATION_TYPE_CROSSSELL = 'crosssell';
    public const RECOMMENDATION_TYPE_FEATURED = 'featured';

    /**
     * Attribute names
     */
    public const ATTRIBUTE_FEATURED_TEMPLATE = 'tweakwise_featured_template';
    public const ATTRIBUTE_UPSELL_TEMPLATE = 'tweakwise_upsell_template';
    public const ATTRIBUTE_UPSELL_GROUP_CODE = 'tweakwise_upsell_group_code';
    public const ATTRIBUTE_CROSSSELL_TEMPLATE = 'tweakwise_crosssell_template';
    public const ATTRIBUTE_CROSSSELL_GROUP_CODE = 'tweakwise_crosssell_group_code';
    public const ATTRIBUTE_FILTER_WHITELIST = 'tweakwise_filter_whitelist';
    public const ATTRIBUTE_FILTER_VALUES_WHITELIST = 'tweakwise_filter_values_whitelist';

    /**
     * @deprecated
     * @see Client::REQUEST_TIMEOUT
     */
    public const REQUEST_TIMEOUT = 5;

    /**
     * @deprecated
     * @see Client\EndpointManager::SERVER_URL
     */
    public const SERVER_URL = 'https://gateway.tweakwisenavigator.net';

    /**
     * @deprecated
     * @see Client\EndpointManager::FALLBACK_SERVER_URL
     */
    public const FALLBACK_SERVER_URL = 'https://gateway.tweakwisenavigator.com';

    /**
     * @var ScopeConfigInterface
     */
    protected $config;

    /**
     * @var Json
     */
    protected $jsonSerializer;

    /**
     * @var bool
     */
    protected $tweakwiseExceptionThrown = false;

    /**
     * @var string[]
     */
    protected $parsedFilterArguments;

    /**
     * @var null|string
     */
    protected $storeId = null;

    /**
     * Export constructor.
     *
     * @param ScopeConfigInterface $config
     * @param Json $jsonSerializer
     * @param RequestInterface $request
     * @param State $state
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function __construct(ScopeConfigInterface $config, Json $jsonSerializer, RequestInterface $request, State $state)
    {
        $this->config = $config;
        $this->jsonSerializer = $jsonSerializer;

        //only do this if its an admin request, to prevent setting the store id in the url in the frontend
        if ($state->getAreaCode() === Area::AREA_ADMINHTML) {
            $this->storeId = $request->getParam('store', null);
        }
    }

    /**
     * @param bool $thrown
     * @return $this
     */
    public function setTweakwiseExceptionThrown($thrown = true)
    {
        $this->tweakwiseExceptionThrown = (bool)$thrown;
        return $this;
    }

    /**
     * @deprecated
     * @see \Emico\Tweakwise\Model\Client\EndpointManager::getServerUrl()
     * @param bool $useFallBack
     * @return string
     */
    public function getGeneralServerUrl(bool $useFallBack = false)
    {
        return $useFallBack
            ? Client\EndpointManager::FALLBACK_SERVER_URL
            : Client\EndpointManager::SERVER_URL;
    }

    /**
     * @param Store|null $store
     * @return string
     */
    public function getGeneralAuthenticationKey(Store $store = null)
    {
        return (string)$this->getStoreConfig('tweakwise/general/authentication_key', $store);
    }

    /**
     * @deprecated
     * @see \Emico\Tweakwise\Model\Client::REQUEST_TIMEOUT
     * @return int
     */
    public function getTimeout()
    {
        return Client::REQUEST_TIMEOUT;
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

        return (bool)$this->getStoreConfig('tweakwise/layered/enabled', $store);
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function isAjaxFilters(Store $store = null)
    {
        return (bool)$this->getStoreConfig('tweakwise/layered/ajax_filters', $store);
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function getCategoryAsLink(Store $store = null)
    {
        return (bool)$this->getStoreConfig('tweakwise/layered/category_links', $store);
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function getHideSingleOptions(Store $store = null)
    {
        return (bool)$this->getStoreConfig('tweakwise/layered/hide_single_option', $store);
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function getUseDefaultLinkRenderer(Store $store = null)
    {
        return (bool) $this->getStoreConfig('tweakwise/layered/default_link_renderer', $store);
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function isFormFilters(Store $store = null)
    {
        return (bool)$this->getStoreConfig('tweakwise/layered/form_filters', $store);
    }

    /**
     * @param Store|null $store
     * @return string
     */
    public function getQueryFilterType(Store $store = null)
    {
        return (string)$this->getStoreConfig('tweakwise/layered/query_filter_type', $store);
    }

    /**
     * @param Store|null $store
     * @return array
     */
    public function getQueryFilterArguments(Store $store = null)
    {
        if ($this->parsedFilterArguments === null) {
            $arguments = $this->getStoreConfig('tweakwise/layered/query_filter_arguments', $store);
            $arguments = explode("\n", $arguments);
            $arguments = array_map('trim', $arguments);
            $arguments = array_filter($arguments);
            $this->parsedFilterArguments = $arguments;
        }
        return $this->parsedFilterArguments;
    }


    /**
     * @param Store|null $store
     * @return string
     */
    public function getQueryFilterRegex(Store $store = null)
    {
        return (string)$this->getStoreConfig('tweakwise/layered/query_filter_regex', $store);
    }

    /**
     * @param Store|null $store
     * @return string
     */
    public function getUrlStrategy(Store $store = null): string
    {
        $urlStrategy = $this->getStoreConfig('tweakwise/layered/url_strategy', $store);
        if (empty($urlStrategy)) {
            $urlStrategy = QueryParameterStrategy::class;
        }
        return $urlStrategy;
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function isAutocompleteEnabled(Store $store = null)
    {
        if ($this->tweakwiseExceptionThrown) {
            return false;
        }

        return (bool)$this->getStoreConfig('tweakwise/autocomplete/enabled', $store);
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function isSuggestionsAutocomplete(Store $store = null)
    {
        return (bool) $this->getStoreConfig('tweakwise/autocomplete/use_suggestions', $store);
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function isAutocompleteProductsEnabled(Store $store = null)
    {
        return (bool)($this->getStoreConfig('tweakwise/autocomplete/show_products', $store) && !$this->isSuggestionsAutocomplete());
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function isAutocompleteSuggestionsEnabled(Store $store = null)
    {
        return (bool)($this->getStoreConfig('tweakwise/autocomplete/show_suggestions', $store) && !$this->isSuggestionsAutocomplete());
    }

    /**
     * @param Store|null $store
     * @return int
     */
    public function showAutocompleteParentCategories(?Store $store = null)
    {
        return (bool)$this->getStoreConfig('tweakwise/autocomplete/show_parent_category', $store);
    }

    /**
     * @param Store|null $store
     * @return int
     */
    public function getAutocompleteMaxResults(Store $store = null)
    {
        return (int)$this->getStoreConfig('tweakwise/autocomplete/max_results', $store);
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function isAutocompleteStayInCategory(Store $store = null)
    {
        return (bool)$this->getStoreConfig('tweakwise/autocomplete/in_current_category', $store);
    }

    /**
     * @param Store|null $store
     * @return int
     */
    public function isSearchEnabled(Store $store = null)
    {
        return (int)$this->getStoreConfig('tweakwise/search/enabled', $store);
    }

    /**
     * @param Store|null $store
     * @return int
     */
    public function getSearchTemplateId(Store $store = null)
    {
        return (int)$this->getStoreConfig('tweakwise/search/template', $store);
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function isPersonalMerchandisingActive(Store $store = null)
    {
        return (bool) $this->getStoreConfig('tweakwise/personal_merchandising/enabled', $store)
            && $this->isAjaxFilters($store);
    }

    /**
     * @param Store|null $store
     * @return string
     */
    public function getPersonalMerchandisingCookieName(Store $store = null)
    {
        return (string) $this->getStoreConfig('tweakwise/personal_merchandising/cookie_name', $store);
    }

    /**
     * @param string $type
     * @param Store|null $store
     * @return bool
     */
    public function isRecommendationsEnabled($type, Store $store = null)
    {
        $this->validateRecommendationType($type);
        return (bool)$this->getStoreConfig(sprintf('tweakwise/recommendations/%s_enabled', $type), $store);
    }

    /**
     * @param string $type
     * @param Store|null $store
     * @return int
     */
    public function getRecommendationsTemplate($type, Store $store = null)
    {
        $this->validateRecommendationType($type);
        return (int)$this->getStoreConfig(sprintf('tweakwise/recommendations/%s_template', $type), $store);
    }

    /**
     * @param string $type
     * @param Store|null $store
     * @return int
     */
    public function getRecommendationsGroupCode($type, Store $store = null)
    {
        $this->validateRecommendationType($type);
        return $this->getStoreConfig(sprintf('tweakwise/recommendations/%s_group_code', $type), $store);
    }

    /**
     * @param Store|null $store
     * @return string
     */
    public function getRecommendationsFeaturedLocation(Store $store = null)
    {
        return (string)$this->getStoreConfig('tweakwise/recommendations/featured_location', $store);
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function isSeoEnabled(Store $store = null)
    {
        return (bool)$this->getStoreConfig('tweakwise/seo/enabled', $store);
    }

    /**
     * @param Store|null $store
     * @return array
     */
    public function getFilterWhitelist(Store $store = null)
    {
        return ConfigAttributeProcessService::extractFilterWhitelist(
            $this->getStoreConfig('tweakwise/seo/filter_whitelist', $store)
        );
    }

    /**
     * @param Store|null $store
     * @return array
     */
    public function getFilterValuesWhitelist(Store $store = null): array
    {
        return ConfigAttributeProcessService::extractFilterValuesWhitelist(
            $this->getStoreConfig('tweakwise/seo/filter_values_whitelist', $store)
        );
    }

    /**
     * @param Store|null $store
     * @return int
     */
    public function getMaxAllowedFacets(Store $store = null)
    {
        return $this->getStoreConfig('tweakwise/seo/max_allowed_facets', $store);
    }

    /**
     * @param Store|null $store
     * @return mixed|string|null
     */
    public function getSearchLanguage(Store $store = null)
    {
        return $this->getStoreConfig('tweakwise/search/language', $store);
    }

    /**
     * @param Store|null $store
     * @param string $path
     * @return mixed|null|string
     */
    protected function getStoreConfig(string $path, Store $store = null)
    {
        if ($store) {
            return $store->getConfig($path);
        }

        return $this->config->getValue($path, ScopeInterface::SCOPE_STORE, $this->storeId);
    }

    /**
     * @param string $type
     */
    protected function validateRecommendationType($type)
    {
        if ($type === self::RECOMMENDATION_TYPE_UPSELL) {
            return;
        }

        if ($type === self::RECOMMENDATION_TYPE_CROSSSELL) {
            return;
        }

        if ($type === self::RECOMMENDATION_TYPE_FEATURED) {
            return;
        }

        throw new InvalidArgumentException(sprintf(
            '$type can be only of type string value: %s, %s, %s',
            self::RECOMMENDATION_TYPE_UPSELL,
            self::RECOMMENDATION_TYPE_CROSSSELL,
            self::RECOMMENDATION_TYPE_FEATURED
        ));
    }

    /**
     * @return string|null
     */
    public function getUserAgentString()
    {
        return $this->getStoreConfig('tweakwise/general/version') ?: null;
    }
}
