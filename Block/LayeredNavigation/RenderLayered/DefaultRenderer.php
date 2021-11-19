<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Block\LayeredNavigation\RenderLayered;

use Emico\Tweakwise\Model\Catalog\Layer\Filter;
use \Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Emico\Tweakwise\Model\Client\Type\FacetType\SettingsType;
use Emico\Tweakwise\Model\Config;
use Emico\Tweakwise\Model\NavigationConfig;
use Emico\Tweakwise\Model\Seo\FilterHelper;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Serialize\Serializer\Json;

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
     * @var NavigationConfig
     */
    protected $navigationConfig;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param Config $config
     * @param NavigationConfig $navigationConfig
     * @param FilterHelper $filterHelper
     * @param Json $jsonSerializer
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        NavigationConfig $navigationConfig,
        FilterHelper $filterHelper,
        Json $jsonSerializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->filterHelper = $filterHelper;
        $this->jsonSerializer = $jsonSerializer;
        $this->navigationConfig = $navigationConfig;
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
     * @param Item $item
     * @return string
     */
    public function getCategoryUrl(Item $item): string
    {
        $catUrl = $this->escapeUrl($item->getUrl());

        if (strpos($catUrl, $this->getBaseUrl()) === false) {
            $catUrl = $this->getBaseUrl() . $item->getUrl();
        }

        return $catUrl;
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
     * @return string
     */
    public function getJsSortConfig()
    {
        return $this->navigationConfig->getJsSortConfig($this->hasAlternateSortOrder());
    }

    /**
     * @return boolean
     */
    public function hasAlternateSortOrder()
    {
        $filter = function (Item $item) {
            return $item->getAlternateSortOrder() !== null;
        };

        $items = $this->getItems();
        $itemsWithAlternateSortOrder = array_filter($items, $filter);

        return \count($items) === \count($itemsWithAlternateSortOrder);
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
    public function getUrlKey()
    {
        return $this->getFacetSettings()->getUrlKey();
    }
}
