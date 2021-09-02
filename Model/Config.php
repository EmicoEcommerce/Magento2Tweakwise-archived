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
use Magento\Framework\App\Config\ScopeConfigInterface;
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
    protected ScopeConfigInterface $config;

    /**
     * @var Json
     */
    protected Json $jsonSerializer;

    /**
     * @var bool
     */
    protected bool $tweakwiseExceptionThrown = false;

    /**
     * @var string[]
     */
    protected array $parsedFilterArguments;

    /**
     * Export constructor.
     *
     * @param ScopeConfigInterface $config
     * @param Json $jsonSerializer
     */
    public function __construct(ScopeConfigInterface $config, Json $jsonSerializer)
    {
        $this->config = $config;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @param bool $thrown
     * @return $this
     */
    public function setTweakwiseExceptionThrown(bool $thrown = true): static
    {
        $this->tweakwiseExceptionThrown = (bool) $thrown;
        return $this;
    }

    /**
     * @deprecated
     * @see \Emico\Tweakwise\Model\Client\EndpointManager::getServerUrl()
     * @param bool $useFallBack
     * @return string
     */
    public function getGeneralServerUrl(bool $useFallBack = false): string
    {
        return $useFallBack
            ? Client\EndpointManager::FALLBACK_SERVER_URL
            : Client\EndpointManager::SERVER_URL;
    }

    /**
     * @param Store|null $store
     * @return string
     */
    public function getGeneralAuthenticationKey(Store $store = null): string
    {
        return (string)$this->getStoreConfig('tweakwise/general/authentication_key', $store);
    }

    /**
     * @deprecated
     * @see \Emico\Tweakwise\Model\Client::REQUEST_TIMEOUT
     * @return int
     */
    public function getTimeout(): int
    {
        return Client::REQUEST_TIMEOUT;
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function isLayeredEnabled(Store $store = null): bool
    {
        if ($this->tweakwiseExceptionThrown) {
            return false;
        }

        return (bool) $this->getStoreConfig('tweakwise/layered/enabled', $store);
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function isAjaxFilters(Store $store = null): bool
    {
        return (bool) $this->getStoreConfig('tweakwise/layered/ajax_filters', $store);
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function getCategoryAsLink(Store $store = null): bool
    {
        return (bool) $this->getStoreConfig('tweakwise/layered/category_links', $store);
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function getHideSingleOptions(Store $store = null): bool
    {
        return (bool) $this->getStoreConfig('tweakwise/layered/hide_single_option', $store);
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function getUseDefaultLinkRenderer(Store $store = null): bool
    {
        return (bool) $this->getStoreConfig('tweakwise/layered/default_link_renderer', $store);
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function isFormFilters(Store $store = null): bool
    {
        return (bool) $this->getStoreConfig('tweakwise/layered/form_filters', $store);
    }

    /**
     * @param Store|null $store
     * @return string
     */
    public function getQueryFilterType(Store $store = null): string
    {
        return (string)$this->getStoreConfig('tweakwise/layered/query_filter_type', $store);
    }

    /**
     * @param Store|null $store
     * @return array
     */
    public function getQueryFilterArguments(Store $store = null): array
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
    public function getQueryFilterRegex(Store $store = null): string
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
    public function isAutocompleteEnabled(Store $store = null): bool
    {
        if ($this->tweakwiseExceptionThrown) {
            return false;
        }

        return (bool) $this->getStoreConfig('tweakwise/autocomplete/enabled', $store);
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function isSuggestionsAutocomplete(Store $store = null): bool
    {
        return (bool) $this->getStoreConfig('tweakwise/autocomplete/use_suggestions', $store);
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function isAutocompleteProductsEnabled(Store $store = null): bool
    {
        return (bool) $this->getStoreConfig('tweakwise/autocomplete/show_products', $store);
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function isAutocompleteSuggestionsEnabled(Store $store = null): bool
    {
        return (bool) $this->getStoreConfig('tweakwise/autocomplete/show_suggestions', $store);
    }

    /**
     * @param Store|null $store
     * @return int
     */
    public function getAutocompleteMaxResults(Store $store = null): int
    {
        return (int)$this->getStoreConfig('tweakwise/autocomplete/max_results', $store);
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function isAutocompleteStayInCategory(Store $store = null): bool
    {
        return (bool) $this->getStoreConfig('tweakwise/autocomplete/in_current_category', $store);
    }

    /**
     * @param Store|null $store
     * @return int
     */
    public function isSearchEnabled(Store $store = null): int
    {
        return (int)$this->getStoreConfig('tweakwise/search/enabled', $store);
    }

    /**
     * @param Store|null $store
     * @return int
     */
    public function getSearchTemplateId(Store $store = null): int
    {
        return (int)$this->getStoreConfig('tweakwise/search/template', $store);
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function isPersonalMerchandisingActive(Store $store = null): bool
    {
        return (bool) $this->getStoreConfig('tweakwise/personal_merchandising/enabled', $store)
            && $this->isAjaxFilters($store);
    }

    /**
     * @param Store|null $store
     * @return string
     */
    public function getPersonalMerchandisingCookieName(Store $store = null): string
    {
        return (string) $this->getStoreConfig('tweakwise/personal_merchandising/cookie_name', $store);
    }

    /**
     * @param string $type
     * @param Store|null $store
     * @return bool
     */
    public function isRecommendationsEnabled($type, Store $store = null): bool
    {
        $this->validateRecommendationType($type);
        return (bool) $this->getStoreConfig(sprintf('tweakwise/recommendations/%s_enabled', $type), $store);
    }

    /**
     * @param string $type
     * @param Store|null $store
     * @return int
     */
    public function getRecommendationsTemplate($type, Store $store = null): int
    {
        $this->validateRecommendationType($type);
        return (int)$this->getStoreConfig(sprintf('tweakwise/recommendations/%s_template', $type), $store);
    }

    /**
     * @param string $type
     * @param Store|null $store
     * @return int
     */
    public function getRecommendationsGroupCode(string $type, Store $store = null): int|string|null
    {
        $this->validateRecommendationType($type);
        return $this->getStoreConfig(sprintf('tweakwise/recommendations/%s_group_code', $type), $store);
    }

    /**
     * @param Store|null $store
     * @return string
     */
    public function getRecommendationsFeaturedLocation(Store $store = null): string
    {
        return (string)$this->getStoreConfig('tweakwise/recommendations/featured_location', $store);
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function isSeoEnabled(Store $store = null): bool
    {
        return (bool) $this->getStoreConfig('tweakwise/seo/enabled', $store);
    }

    /**
     * @param Store|null $store
     * @return array
     */
    public function getFilterWhitelist(Store $store = null): array
    {
        $filterList = $this->getStoreConfig('tweakwise/seo/filter_whitelist', $store);
        return explode(',', $filterList) ?: [];
    }

    /**
     * @param Store|null $store
     * @return int
     */
    public function getMaxAllowedFacets(Store $store = null): int|string|null
    {
        return $this->getStoreConfig('tweakwise/seo/max_allowed_facets', $store);
    }

    /**
     * @param Store|null $store
     * @return mixed
     */
    public function getSearchLanguage(Store $store = null): mixed
    {
        return $this->getStoreConfig('tweakwise/search/language', $store);
    }

    /**
     * @param string $path
     * @param Store|null $store
     * @return mixed
     */
    protected function getStoreConfig(string $path, Store $store = null): mixed
    {
        if ($store) {
            return $store->getConfig($path);
        }

        return $this->config->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param string $type
     */
    protected function validateRecommendationType(string $type)
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
    public function getUserAgentString(): ?string
    {
        return $this->getStoreConfig('tweakwise/general/version') ?: null;
    }
}
