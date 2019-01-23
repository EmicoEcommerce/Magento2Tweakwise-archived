<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */

namespace Emico\Tweakwise\Model\Seo;

use Emico\Tweakwise\Model\Catalog\Layer\Filter;
use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Emico\Tweakwise\Model\Catalog\Layer\FilterList\Tweakwise;
use Emico\Tweakwise\Model\Config;
use Magento\Catalog\Model\Layer\Resolver;

class FilterHelper
{
    /**
     *
     */
    const TWEAKWISE_CATEGORY_FILTER_NAME = 'Categorie';

    /**
     * @var Resolver
     */
    protected $layerResolver;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Tweakwise
     */
    protected $tweakwiseFilterList;

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
     * @param Filter $item
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
     * @param Item $item
     * @return bool
     */
    protected function isCategoryFilterItem(Item $item): bool
    {
        return $this->getAttributeCodeFromFilterItem($item) === self::TWEAKWISE_CATEGORY_FILTER_NAME;
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
            ->getTitle();
    }

    /**
     * @return bool
     */
    protected function exceedsMaxAllowedFacets(): bool
    {
        $maxAllowedFacetsCount = $this->config->getMaxAllowedFacets();
        $layer = $this->layerResolver->get();
        $filters = $this->tweakwiseFilterList->getFilters($layer);

        $selectedFilterCount = \array_sum(
            \array_map(
                function (Filter $filter) {
                    return \count($filter->getActiveItems());
                },
                $filters
            )
        );

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
}