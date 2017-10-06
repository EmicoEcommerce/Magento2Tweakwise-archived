<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\FilterList;

use Emico\Tweakwise\Model\Catalog\Layer\FilterFactory;
use Emico\Tweakwise\Model\Catalog\Layer\NavigationContext\CurrentContext;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;

class Tweakwise
{
    /**
     * @var FilterInterface[]
     */
    protected $filters;

    /**
     * @var FilterFactory
     */
    protected $filterFactory;

    /**
     * @var CurrentContext
     */
    protected $context;

    /**
     * Tweakwise constructor.
     *
     * @param FilterFactory $filterFactory
     * @param CurrentContext $context
     */
    public function __construct(FilterFactory $filterFactory, CurrentContext $context)
    {
        $this->filterFactory = $filterFactory;
        $this->context = $context;
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

        $request = $this->context->getRequest();
        $request->addCategoryFilter($layer->getCurrentCategory());

        $facets = $this->context->getResponse()->getFacets();

        $navigationContext = $this->context->getContext();
        $filterAttributes = $navigationContext->getFilterAttributeMap();
        $this->filters = [];
        foreach ($facets as $facet) {
            $key = $facet->getFacetSettings()->getUrlKey();
            $attribute = isset($filterAttributes[$key]) ? $filterAttributes[$key] : null;

            $this->filters[] = $this->filterFactory->create(['facet' => $facet, 'layer' => $layer, 'attribute' => $attribute]);
        }

        return $this;
    }
}