<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Block\LayeredNavigation\RenderLayered;

use Emico\Tweakwise\Model\Catalog\Layer\Filter;
use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Emico\Tweakwise\Model\Client\Type\FacetType\SettingsType;
use Emico\Tweakwise\Model\Config;
use Emico\Tweakwise\Model\Seo\FilterHelper;
use Magento\Framework\View\Element\Template;

class DefaultRenderer extends Template
{
    use AnchorRendererTrait;

    /**
     * {@inheritDoc}
     */
    protected $_template = 'Emico_Tweakwise::product/layered/default.phtml';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var Json
     */
    protected $jsonSerializer;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param Config $config
     * @param Json $jsonSerializer
     * @param array $data
     */
    public function __construct(Template\Context $context, Config $config, FilterHelper $filterHelper, array $data = [])
    {
        $this->config = $config;
        $this->filterHelper = $filterHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param Filter $filter
     */
    public function setFilter(Filter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @return SettingsType
     */
    protected function getFacetSettings()
    {
        return $this->filter->getFacet()->getFacetSettings();
    }

    /**
     * @return Item[]
     */
    public function getItems()
    {
        $items = $this->filter->getItems();
        $maxItems = $this->getMaxItemsShown();

        /** @var Item $item */
        foreach ($items as $index => $item) {
            $defaultShow = $index >= $maxItems;
            $item->setData('_default_hidden', $defaultShow);
        }

        return $items;
    }

    /**
     * @param Item $item
     * @return string
     */
    protected function getItemUrl(Item $item)
    {
        return $this->escapeHtml($item->getUrl());
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function itemDefaultHidden(Item $item)
    {
        return (bool) $item->getData('_default_hidden');
    }

    /**
     * @return int
     */
    public function getMaxItemsShown()
    {
        return $this->getFacetSettings()->getNumberOfShownAttributes();
    }

    /**
     * @return bool
     */
    public function hasHiddenItems()
    {
        return count($this->getItems()) > $this->getMaxItemsShown();
    }

    /**
     * @return string
     */
    public function getMoreItemText()
    {
        $text = $this->getFacetSettings()->getExpandText();
        if ($text) {
            return $text;
        }

        return 'Meer filters tonen';
    }

    /**
     * @return string
     */
    public function getLessItemText()
    {
        $text = $this->getFacetSettings()->getCollapseText();
        if ($text) {
            return $text;
        }

        return 'Minder filters tonen';
    }

    /**
     * @return bool
     */
    public function shouldDisplayProductCountOnLayer()
    {
        return $this->getFacetSettings()->getIsNumberOfResultVisible();
    }

    /**
     * @return string
     */
    public function getCssId()
    {
        return spl_object_hash($this);
    }

    /**
     * @param Item $item
     * @return string
     */
    public function getCssItemId(Item $item)
    {
        return spl_object_hash($item);
    }

    /**
     * @return bool
     */
    public function showCheckbox()
    {
        return $this->getFacetSettings()->getSelectionType() === SettingsType::SELECTION_TYPE_CHECKBOX;
    }

    /**
     * @return string
     */
    public function getItemPrefix()
    {
        return $this->escapeHtml($this->getFacetSettings()->getPrefix());
    }

    /**
     * @return string
     */
    public function getItemPostfix()
    {
        return $this->escapeHtml($this->getFacetSettings()->getPostfix());
    }

    /**
     * @return string
     */
    public function getJsNavigationConfig(): string
    {
        return $this->config->getJsNavigationConfig();
    }

    /**
     * @return Config
     */
    public function getJsUseFormFilters()
    {
        return $this->config->getJsUseFormFilters();
    }

    /**
     * @return string
     */
    public function getUrlKey()
    {
        return $this->getFacetSettings()->getUrlKey();
    }
}