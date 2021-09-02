<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\FilterList;

use Emico\Tweakwise\Model\Catalog\Layer\Filter;
use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Emico\Tweakwise\Model\Catalog\Layer\FilterFactory;
use Emico\Tweakwise\Model\Catalog\Layer\NavigationContext\CurrentContext;
use Emico\Tweakwise\Model\Client\Request\ProductNavigationRequest;
use Emico\Tweakwise\Model\Client\Request\ProductSearchRequest;
use Emico\Tweakwise\Model\Client\Type\FacetType\SettingsType;
use Emico\Tweakwise\Model\Config;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\Eav\Model\Entity\Attribute;
use Emico\Tweakwise\Model\Client\Type\FacetType;

class Tweakwise
{
    /**
     * @var FilterInterface[]
     */
    protected array $filters;

    /**
     * @var FilterFactory
     */
    protected FilterFactory $filterFactory;

    /**
     * @var CurrentContext
     */
    protected CurrentContext $context;

    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @var AttributeFactory
     */
    protected AttributeFactory $attributeFactory;

    /**
     * Tweakwise constructor.
     *
     * @param FilterFactory $filterFactory
     * @param CurrentContext $context
     * @param Config $config
     * @param AttributeFactory $attributeFactory
     */
    public function __construct(
        FilterFactory $filterFactory,
        CurrentContext $context,
        Config $config,
        AttributeFactory $attributeFactory
    ) {
        $this->filterFactory = $filterFactory;
        $this->context = $context;
        $this->config = $config;
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * @param Layer $layer
     * @return FilterInterface[]
     */
    public function getFilters(Layer $layer): array
    {
        if (!$this->filters) {
            $this->initFilters($layer);
        }

        return $this->filters;
    }

    /**
     * @param Layer $layer
     */
    protected function initFilters(Layer $layer)
    {
        $request = $this->context->getRequest();
        if (!$request->hasParameter('tn_cid')) {
            $request->addCategoryFilter($layer->getCurrentCategory());
        }

        $facets = $this->context->getResponse()->getFacets();

        $facetAttributeNames = array_map(
            static function (FacetType $facet) {
                return $facet->getFacetSettings()->getAttributename();
            },
            $facets
        );

        $filterAttributes = $this->context
            ->getContext()
            ->getFilterAttributeMap($facetAttributeNames);

        $this->filters = [];
        foreach ($facets as $facet) {
            $attributeName = $facet->getFacetSettings()->getAttributename();
            $attribute = $filterAttributes[$attributeName]
                ?? $this->mockAttributeModel($attributeName);

            $filter = $this->filterFactory->create(
                [
                    'facet' => $facet,
                    'layer' => $layer,
                    'attribute' => $attribute
                ]
            );
            if ($this->shouldHideFacet($filter)) {
                continue;
            }

            $this->filters[] = $filter;

            foreach ($filter->getActiveItems() as $activeFilterItem) {
                if ($this->shouldHideActiveFilterItem($activeFilterItem, $request)) {
                    continue;
                }
                $layer->getState()->addFilter($activeFilterItem);
            }
        }
    }

    /**
     * @param Item $activeFilterItem
     * @param ProductNavigationRequest $request
     * @return bool
     */
    protected function shouldHideActiveFilterItem(Item $activeFilterItem, ProductNavigationRequest $request): bool
    {
        $source = $activeFilterItem
            ->getFilter()
            ->getFacet()
            ->getFacetSettings()
            ->getSource();
        // Add active category filter only on search pages
        $isCategory = $source === SettingsType::SOURCE_CATEGORY;
        if (!$isCategory) {
            return false;
        }

        // Add active category filter only on search pages
        return !($request instanceof ProductSearchRequest);
    }

    /**
     * @param Filter $filter
     * @return bool
     */
    protected function shouldHideFacet(Filter $filter): bool
    {
        if (!$this->config->getHideSingleOptions()) {
            return false;
        }

        return count($filter->getItems()) === 1;
    }

    /**
     * @param string $attributeName
     * @return Attribute
     */
    protected function mockAttributeModel(string $attributeName): Attribute
    {
        /** @var Attribute $attributeModel */
        $attributeModel = $this->attributeFactory->create();
        $attributeModel->setAttributeCode($attributeName);

        return $attributeModel;
    }
}
