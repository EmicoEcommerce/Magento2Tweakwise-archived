<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\FilterList;

use Emico\Tweakwise\Model\Catalog\Layer\Filter;
use Emico\Tweakwise\Model\Catalog\Layer\FilterFactory;
use Emico\Tweakwise\Model\Catalog\Layer\NavigationContext\CurrentContext;
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
    private $filters;

    /**
     * @var FilterFactory
     */
    private $filterFactory;

    /**
     * @var CurrentContext
     */
    private $context;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var AttributeFactory
     */
    private $attributeFactory;

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
        $request->addCategoryFilter($layer->getCurrentCategory());

        $facets = $this->context->getResponse()->getFacets();

        $facetAttributeNames = array_map(
            function (FacetType $facet) {
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
                $layer->getState()->addFilter($activeFilterItem);
            }
        }
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
