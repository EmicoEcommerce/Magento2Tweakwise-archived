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
use Emico\Tweakwise\Model\Client\Type\FacetType;
use Emico\Tweakwise\Model\Config;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;

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
     * Tweakwise constructor.
     *
     * @param FilterFactory $filterFactory
     * @param CurrentContext $context
     */
    public function __construct(FilterFactory $filterFactory, CurrentContext $context, Config $config)
    {
        $this->filterFactory = $filterFactory;
        $this->context = $context;
        $this->config = $config;
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
            if (!$this->shouldHideFacet($facet)) {
                continue;
            }

            $key = $facet->getFacetSettings()->getUrlKey();
            $attribute = isset($filterAttributes[$key]) ? $filterAttributes[$key] : null;

            $this->filters[] = $this->filterFactory->create(['facet' => $facet, 'layer' => $layer, 'attribute' => $attribute]);
        }

        return $this;
    }

    /**
     * @param FacetType $facet
     * @return bool
     */
    protected function shouldHideFacet(FacetType $facet)
    {
        if (!$this->config->getHideSingleOptions()) {
            return false;
        }

        return count($facet->getAttributes()) === 1;
    }
}