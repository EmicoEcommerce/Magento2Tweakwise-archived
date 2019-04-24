<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\Filter;

use Emico\Tweakwise\Model\Catalog\Layer\Filter;
use Emico\Tweakwise\Model\Catalog\Layer\Url;
use Emico\Tweakwise\Model\Client\Type\AttributeType;
use Emico\Tweakwise\Model\Client\Type\FacetType\SettingsType;
use Magento\Catalog\Model\Layer\Filter\Item as MagentoItem;

/**
 * Class Item extends Magento\Catalog\Model\Layer\Filter\Item only for the type hint in Magento\Swatches\Block\LayeredNavigation\RenderLayered
 *
 * @package Emico\Tweakwise\Model\Catalog\Layer\Filter
 */
class Item extends MagentoItem
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var AttributeType
     */
    protected $attributeType;

    /**
     * @var Url
     */
    protected $url;

    /**
     * @var Item[]
     */
    protected $children = [];

    /**
     * Item constructor.
     *
     * @param Filter $filter
     * @param AttributeType $attributeType
     * @param Url $url
     */
    public function __construct(Filter $filter, AttributeType $attributeType, Url $url)
    {
        $this->filter = $filter;
        $this->attributeType = $attributeType;
        $this->url = $url;
    }

    /**
     * @return Filter
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        $title = (string) $this->attributeType->getTitle();
        return htmlentities($title);
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return (int) $this->attributeType->getNumberOfResults();
    }

    /**
     * @return bool
     */
    public function isSelected()
    {
        return (bool) $this->attributeType->getIsSelected();
    }

    /**
     * @return int
     */
    public function getAlternateSortOrder()
    {
        return $this->attributeType->getAlternateSortOrder();
    }

    /**
     * Method is called for swatches and should return the original value instead of the label.
     * Used for the default swatch renderer
     *
     * @return int
     */
    public function getValue()
    {
        return $this->getFilter()->getOptionIdByLabel($this->getLabel());
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $settings = $this->getFilter()->getFacet()->getFacetSettings();
        if ($settings->getSelectionType() === SettingsType::SELECTION_TYPE_SLIDER) {
            return $this->url->getSliderUrl($this);
        }

        if ($this->isSelected()) {
            return $this->url->getRemoveFilter($this);
        }

        return $this->url->getSelectFilter($this);
    }

    /**
     * Get url for remove item from filter
     *
     * @return string
     */
    public function getRemoveUrl()
    {
        return $this->url->getRemoveFilter($this);
    }

    /**
     * @return AttributeType
     */
    public function getAttribute()
    {
        return $this->attributeType;
    }

    /**
     * @param Item[] $children
     * @return $this
     */
    public function setChildren(array $children)
    {
        $this->children = $children;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return count($this->children) > 0;
    }

    /**
     * @return Item[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->getAttribute()->getIsSelected();
    }
}