<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model;

use Emico\Tweakwise\Exception\InvalidArgumentException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\Store;

class Config
{
    /**
     * Recommendation types
     */
    const RECOMMENDATION_TYPE_UPSELL = 'upsell';
    const RECOMMENDATION_TYPE_CROSSSELL = 'crosssell';
    const RECOMMENDATION_TYPE_FEATURED = 'featured';

    /**
     * Attribute names
     */
    const ATTRIBUTE_FEATURED_TEMPLATE = 'tweakwise_featured_template';
    const ATTRIBUTE_UPSELL_TEMPLATE = 'tweakwise_upsell_template';
    const ATTRIBUTE_UPSELL_GROUP_CODE = 'tweakwise_upsell_group_code';
    const ATTRIBUTE_CROSSSELL_TEMPLATE = 'tweakwise_crosssell_template';
    const ATTRIBUTE_CROSSSELL_GROUP_CODE = 'tweakwise_crosssell_group_code';

    /**
     * @var ScopeConfigInterface
     */
    protected $config;

    /**
     * @var bool
     */
    protected $tweakwiseExceptionThrown = false;

    /**
     * @var string[]
     */
    protected $parsedFilterArguments;

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
        return (int) $this->getStoreConfig($store, 'tweakwise/general/timeout');
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
     * @return bool
     */
    public function getUseFormFilters(Store $store = null)
    {
        return (bool) $this->getStoreConfig($store, 'tweakwise/layered/form_filters');
    }

    /**
     * @param Store|null $store
     * @return string
     */
    public function getQueryFilterType(Store $store = null)
    {
        return (string) $this->getStoreConfig($store, 'tweakwise/layered/query_filter_type');
    }

    /**
     * @param Store|null $store
     * @return array
     */
    public function getQueryFilterArguments(Store $store = null)
    {
        if ($this->parsedFilterArguments === null) {
            $arguments = $this->getStoreConfig($store, 'tweakwise/layered/query_filter_arguments');
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
        return (string) $this->getStoreConfig($store, 'tweakwise/layered/query_filter_regex');
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

        return (bool) $this->getStoreConfig($store, 'tweakwise/autocomplete/enabled');
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function isAutocompleteProductsEnabled(Store $store = null)
    {
        return (bool) $this->getStoreConfig($store, 'tweakwise/autocomplete/show_products');
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function isAutocompleteSuggestionsEnabled(Store $store = null)
    {
        return (bool) $this->getStoreConfig($store, 'tweakwise/autocomplete/show_suggestions');
    }

    /**
     * @param Store|null $store
     * @return int
     */
    public function getAutocompleteMaxResults(Store $store = null)
    {
        return (int) $this->getStoreConfig($store, 'tweakwise/autocomplete/max_results');
    }

    /**
     * @param Store|null $store
     * @return bool
     */
    public function isAutocompleteStayInCategory(Store $store = null)
    {
        return (bool) $this->getStoreConfig($store, 'tweakwise/autocomplete/in_current_category');
    }

    /**
     * @param Store|null $store
     * @return int
     */
    public function isSearchEnabled(Store $store = null)
    {
        return (int) $this->getStoreConfig($store, 'tweakwise/search/enabled');
    }

    /**
     * @param Store|null $store
     * @return int
     */
    public function getSearchTemplateId(Store $store = null)
    {
        return (int) $this->getStoreConfig($store, 'tweakwise/search/template');
    }

    /**
     * @param string $type
     * @param Store|null $store
     * @return bool
     */
    public function isRecommendationsEnabled($type, Store $store = null)
    {
        $this->validateRecommendationType($type);
        return (bool) $this->getStoreConfig($store, sprintf('tweakwise/recommendations/%s_enabled', $type));
    }

    /**
     * @param string $type
     * @param Store|null $store
     * @return int
     */
    public function getRecommendationsTemplate($type, Store $store = null)
    {
        $this->validateRecommendationType($type);
        return (int) $this->getStoreConfig($store, sprintf('tweakwise/recommendations/%s_template', $type));
    }

    /**
     * @param string $type
     * @param Store|null $store
     * @return int
     */
    public function getRecommendationsGroupCode($type, Store $store = null)
    {
        $this->validateRecommendationType($type);
        return $this->getStoreConfig($store, sprintf('tweakwise/recommendations/%s_group_code', $type));
    }

    /**
     * @param Store|null $store
     * @return string
     */
    public function getRecommendationsFeaturedLocation(Store $store = null)
    {
        return (string) $this->getStoreConfig($store, 'tweakwise/recommendations/featured_location');
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
}