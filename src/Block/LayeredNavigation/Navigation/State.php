<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Block\LayeredNavigation\Navigation;

use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Emico\Tweakwise\Model\Client\Type\FacetType\SettingsType;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\View\Element\Template\Context;
use Magento\LayeredNavigation\Block\Navigation\State as MagentoStateBlock;

class State extends MagentoStateBlock
{
    /**
     * State constructor.
     * @param Context $context
     * @param Resolver $layerResolver
     * @param array $data
     */
    public function __construct(
        Context $context,
        Resolver $layerResolver,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $layerResolver,
            $data
        );
    }

    /**
     * @param Item $item
     * @return string|void
     */
    public function getActiveFilterCssId(Item $item)
    {
        $facetSettings = $item->getFilter()->getFacet()->getFacetSettings();
        if ($facetSettings->getSelectionType() === SettingsType::SELECTION_TYPE_SLIDER) {
            return 'slider-' . $facetSettings->getUrlKey();
        }

        return spl_object_hash($item);
    }
}
