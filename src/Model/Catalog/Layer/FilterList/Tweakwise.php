<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\FilterList;

use Emico\Tweakwise\Model\Catalog\Layer\NavigationContext;
use Emico\Tweakwise\Model\Catalog\Layer\FilterFactory;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;

class Tweakwise
{
    /**
     * @var NavigationContext
     */
    protected $navigationContext;

    /**
     * @var FilterInterface[]
     */
    protected $filters;

    /**
     * @var FilterFactory
     */
    protected $filterFactory;

    /**
     * Tweakwise constructor.
     *
     * @param NavigationContext $navigationContext
     * @param FilterFactory $filterFactory
     */
    public function __construct(NavigationContext $navigationContext, FilterFactory $filterFactory)
    {
        $this->navigationContext = $navigationContext;
        $this->filterFactory = $filterFactory;
    }

    /**
     * @param Layer $layer
     * @return FilterInterface[]
     */
    public function getFilters(Layer $layer)
    {
        if (!$this->filters) {
            $this->initFilters($layer);
        }

        return $this->filters;
    }

    /**
     * @param Layer $layer
     * @return $this
     */
    protected function initFilters(Layer $layer)
    {
        $request = $this->navigationContext->getRequest();
        $request->addCategoryFilter($layer->getCurrentCategory());

        $facets = $this->navigationContext->getResponse()
            ->getFacets();

        $filterAttributes = $this->navigationContext->getFilterAttributeMap();
        $this->filters = [];
        foreach ($facets as $facet) {
            $key = $facet->getFacetSettings()->getUrlKey();
            $attribute = isset($filterAttributes[$key]) ? $filterAttributes[$key] : null;

            $this->filters[] = $this->filterFactory->create(['facet' => $facet, 'layer' => $layer, 'attribute' => $attribute]);
        }

        return $this;
    }
}