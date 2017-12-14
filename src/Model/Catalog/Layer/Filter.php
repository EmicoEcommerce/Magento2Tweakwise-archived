<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer;

use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Emico\Tweakwise\Model\Client\Type\AttributeType;
use Emico\Tweakwise\Model\Client\Type\FacetType;
use Emico\Tweakwise\Model\Catalog\Layer\Filter\ItemFactory;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Eav\Model\Entity\Attribute\Option;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManager;

/**
 * Class Filter Extends Magento\Catalog\Model\Layer\Filter\AbstractFilter only for the type hint in Magento\Swatches\Block\LayeredNavigation\RenderLayered
 *
 * @package Emico\Tweakwise\Model\Catalog\Layer
 */
class Filter extends AbstractFilter implements FilterInterface
{
    /**
     * @var string
     */
    private $requestVar;

    /**
     * @var
     */
    private $items;

    /**
     * @var Layer
     */
    private $layer;

    /**
     * @var Attribute
     */
    private $attributeModel;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var int
     */
    private $websiteId;

    /**
     * @var FacetType
     */
    private $facet;

    /**
     * @var ItemFactory
     */
    private $itemFactory;

    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * @var int[]
     */
    private $optionLabelValueMap;

    /**
     * @var Item[]
     */
    private $optionLabelItemMap;

    /**
     * Filter constructor.
     *
     * @param Layer $layer
     * @param FacetType $facet
     * @param ItemFactory $itemFactory
     * @param StoreManager $storeManager
     * @param Attribute|null $attribute
     */
    public function __construct(Layer $layer, FacetType $facet, ItemFactory $itemFactory, StoreManager $storeManager, Attribute $attribute = null)
    {
        $this->layer = $layer;
        $this->facet = $facet;
        $this->itemFactory = $itemFactory;
        $this->storeManager = $storeManager;
        $this->attributeModel = $attribute;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequestVar($varName)
    {
        $this->requestVar = $varName;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestVar()
    {
        return $this->requestVar;
    }

    /**
     * {@inheritdoc}
     */
    public function getResetValue()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getCleanValue()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(RequestInterface $request)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsCount()
    {
        return count($this->getItems());
    }

    /**
     * @return Item[]
     * {@inheritdoc}
     */
    public function getItems()
    {
        if (!$this->items) {
            $this->initItems();
        }

        return $this->items;
    }

    public function getVisibleItems()
    {

    }

    /**
     * @param AttributeType $item
     * @return $this
     */
    public function addItem(AttributeType $item)
    {
        $this->items[] = $this->createItem($item);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setItems(array $items)
    {
        $this->items = [];
        foreach ($items as $item) {
            $this->addItem($item);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLayer()
    {
        return $this->layer;
    }

    /**
     * @return Item[]
     */
    public function getActiveItems()
    {
        $result = [];
        foreach ($this->getItems() as $item) {
            if ($item->isActive()) {
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributeModel($attribute)
    {
        $this->attributeModel = $attribute;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeModel()
    {
        return $this->attributeModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->facet->getFacetSettings()->getTitle();
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        if ($this->storeId === null) {
            $this->storeId = $this->storeManager->getStore()->getId();
        }
        return $this->storeId;
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        $this->storeId = (int) $storeId;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getWebsiteId()
    {
        if ($this->websiteId === null) {
            $this->websiteId = $this->storeManager->getStore()->getWebsiteId();
        }
        return $this->websiteId;
    }

    /**
     * {@inheritdoc}
     */
    public function setWebsiteId($websiteId)
    {
        $this->websiteId = (int) $websiteId;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getClearLinkText()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function hasAttributeModel()
    {
        return $this->attributeModel !== null;
    }

    /**
     * @return FacetType
     */
    public function getFacet()
    {
        return $this->facet;
    }

    /**
     * @param AttributeType $attributeType
     * @return Item
     */
    private function createItem(AttributeType $attributeType)
    {
        $item = $this->itemFactory->create(['filter' => $this, 'attributeType' => $attributeType]);

        $children = [];
        foreach ($attributeType->getChildren() as $childAttributeType) {
            $children[] = $this->itemFactory->create(['filter' => $this, 'attributeType' => $childAttributeType]);
        }
        $item->setChildren($children);

        return $item;
    }

    /**
     * @return $this
     */
    private function initItems()
    {
        foreach ($this->facet->getAttributes() as $attribute) {
            $this->addItem($attribute);
        }
        return $this;
    }

    /**
     * @return int[]
     */
    private function getOptionLabelValueMap()
    {
        if (!$this->hasAttributeModel()) {
            return [];
        }

        if ($this->optionLabelValueMap === null) {
            $map = [];
            /** @var Option $option */
            foreach ($this->getAttributeModel()->getOptions() as $option) {
                $map[$option->getLabel()] = $option->getValue();
            }

            $this->optionLabelValueMap = $map;
        }
        return $this->optionLabelValueMap;
    }

    /**
     * @return Item[]
     */
    private function getOptionLabelItemMap()
    {
        if (!$this->hasAttributeModel()) {
            return [];
        }

        if ($this->optionLabelItemMap === null) {
            $map = [];
            /** @var Item $item */
            foreach ($this->getItems() as $item) {
                $map[$item->getLabel()] = $item;
            }

            $this->optionLabelItemMap = $map;
        }
        return $this->optionLabelItemMap;
    }

    /**
     * @param string $label
     * @return int|null
     */
    private function getOptionIdByLabel($label)
    {
        $map = $this->getOptionLabelValueMap();
        return isset($map[$label]) ? $map[$label] : null;
    }

    /**
     * @param int $id
     * @return string|null
     */
    private function getLabelByOptionId($id)
    {
        $map = $this->getOptionLabelValueMap();
        $map = array_flip($map);
        return isset($map[$id]) ? $map[$id] : null;
    }

    /**
     * @param int $optionId
     * @return Item|null
     */
    private function getItemByOptionId($optionId)
    {
        $label = $this->getLabelByOptionId($optionId);
        if (!$label) {
            return null;
        }

        $map = $this->getOptionLabelItemMap();
        return isset($map[$label]) ? $map[$label] : null;
    }

    /**
     * @return bool
     */
    private function isCollapsible()
    {
        return $this->facet->getFacetSettings()->getIsCollapsible();
    }

    /**
     * @return bool
     */
    private function isDefaultCollapsed()
    {
        return $this->facet->getFacetSettings()->getIsCollapsed();
    }

    /**
     * @return string
     */
    private function getCssClass()
    {
        return $this->facet->getFacetSettings()->getCssClass();
    }

    /**
     * @return string
     */
    private function getTooltip()
    {
        return $this->facet->getFacetSettings()->getInfoText();
    }
}