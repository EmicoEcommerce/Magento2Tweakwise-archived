<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */

namespace Emico\Tweakwise\Model\Seo;

use Emico\Tweakwise\Model\Catalog\Layer\Filter;
use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Emico\Tweakwise\Model\Catalog\Layer\FilterList\Tweakwise;
use Emico\Tweakwise\Model\Client\Type\FacetType\SettingsType;
use Emico\Tweakwise\Model\Config;
use Magento\Catalog\Model\Layer\Resolver;

class FilterHelper
{
    /**
     *
     */
    public const TWEAKWISE_CATEGORY_FILTER_NAME = 'categorie';

    /**
     * @var Resolver
     */
    protected Resolver $layerResolver;

    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @var Tweakwise
     */
    protected Tweakwise $tweakwiseFilterList;

    /**
     * FilterHelper constructor.
     * @param Resolver $layerResolver
     * @param Tweakwise $filterList
     * @param Config $config
     */
    public function __construct(Resolver $layerResolver, Tweakwise $filterList, Config $config)
    {
        $this->layerResolver = $layerResolver;
        $this->tweakwiseFilterList = $filterList;
        $this->config = $config;
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function shouldFilterBeIndexable(Item $item): bool
    {
        if (!$this->config->isSeoEnabled()) {
            return true;
        }

        if ($this->isCategoryFilterItem($item)) {
            return true;
        }

        if (!$this->exceedsMaxAllowedFacets() && $this->isFilterItemInWhiteList($item)) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function shouldPageBeIndexable(): bool
    {
        foreach ($this->getActiveFilterItems() as $item) {
            if (!$this->shouldFilterBeIndexable($item)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Item $item
     * @return bool
     */
    protected function isCategoryFilterItem(Item $item): bool
    {
        return $item->getFilter()->getFacet()->getFacetSettings()->getSource() === SettingsType::SOURCE_CATEGORY;
    }

    /**
     * @param Item $item
     * @return string
     */
    protected function getAttributeCodeFromFilterItem(Item $item)
    {
        return $item->getFilter()
            ->getFacet()
            ->getFacetSettings()
            ->getUrlKey();
    }

    /**
     * @return bool
     */
    protected function exceedsMaxAllowedFacets(): bool
    {
        $maxAllowedFacetsCount = $this->config->getMaxAllowedFacets();
        if (!is_numeric($maxAllowedFacetsCount)) {
            return false;
        }

        $maxAllowedFacetsCount = (int) $maxAllowedFacetsCount;
        $selectedFilterCount = \count($this->getActiveFilterItems(false));

        return $selectedFilterCount > $maxAllowedFacetsCount;
    }

    /**
     * @param Item $item
     * @return bool
     */
    protected function isFilterItemInWhiteList(Item $item): bool
    {
        $filterWhiteList = $this->config->getFilterWhitelist();
        $attributeCode = $this->getAttributeCodeFromFilterItem($item);

        return \in_array($attributeCode, $filterWhiteList, true);
    }

    /**
     * @param bool $includeCategoryFilter
     * @return Item[]
     */
    protected function getActiveFilterItems(bool $includeCategoryFilter = true): array
    {
        $layer = $this->layerResolver->get();
        $filters = $layer->getState()->getFilters();
        if ($includeCategoryFilter) {
            return $filters;
        }

        return array_filter(
            $filters,
            function (Item $filterItem) {
                return !$this->isCategoryFilterItem($filterItem);
            }
        );
    }
}
