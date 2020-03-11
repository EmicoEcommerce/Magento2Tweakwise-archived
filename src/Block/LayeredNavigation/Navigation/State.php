<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Block\LayeredNavigation\Navigation;

use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Emico\Tweakwise\Model\Client\Request\ProductSearchRequest;
use Emico\Tweakwise\Model\Client\Type\FacetType\SettingsType;
use Emico\Tweakwise\Model\Config;
use Emico\Tweakwise\Model\Catalog\Layer\Url;
use Emico\Tweakwise\Model\Catalog\Layer\NavigationContext\CurrentContext;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\View\Element\Template\Context;
use Magento\LayeredNavigation\Block\Navigation\State as MagentoStateBlock;

class State extends MagentoStateBlock
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Url
     */
    protected $url;

    /**
     * State constructor.
     * @param Context $context
     * @param Resolver $layerResolver
     * @param Config $config
     * @param Url $url
     * @param CurrentContext $currentContext
     * @param array $data
     */
    public function __construct(
        Context $context,
        Resolver $layerResolver,
        Config $config,
        Url $url,
        CurrentContext $currentContext,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $layerResolver,
            $data
        );

        $this->config = $config;
        $this->url = $url;
        $this->updateTemplate($currentContext);
    }

    /**
     * Use our template if applicable
     * If you want to change this behaviour use a plugin on afterGetTemplate
     *
     * @param CurrentContext $currentContext
     */
    protected function updateTemplate(CurrentContext $currentContext)
    {
        if ($this->config->getUseDefaultLinkRenderer()) {
            return;
        }

        $searchEnabled = $this->config->isSearchEnabled();
        $navigationEnabled = $this->config->isLayeredEnabled();

        $isSearch = $currentContext->getRequest() instanceof ProductSearchRequest;
        $isNavigation = !$isSearch;

        if ($isSearch && $searchEnabled) {
            $this->_template = 'Emico_Tweakwise::layer/state.phtml';
        }

        if ($isNavigation && $navigationEnabled) {
            $this->_template = 'Emico_Tweakwise::layer/state.phtml';
        }
    }

    /**
     * @return string
     */
    public function getClearUrl()
    {
        if (!$this->config->isLayeredEnabled()) {
            return parent::getClearUrl();
        }

        return $this->url->getClearUrl($this->getActiveFilters());
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
