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
use Emico\Tweakwise\Model\Seo\FilterHelper;
use Emico\Tweakwise\Model\Swatches\SwatchAttributeResolver;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory as EavAttributeFactory;
use Magento\Catalog\Model\ResourceModel\Layer\Filter\AttributeFactory;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Swatches\Block\LayeredNavigation\RenderLayered;
use Magento\Swatches\Helper\Data;
use Magento\Swatches\Helper\Media;

class SwatchRenderer extends RenderLayered
{
    use AnchorRendererTrait;

    /**
     * Path to template file.
     *
     * @var string
     */
    protected $_template = 'Emico_Tweakwise::product/layered/swatch.phtml';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var EavAttributeFactory
     */
    protected $eavAttributeFactory;

    /**
     * @var SwatchAttributeResolver
     */
    protected $swatchAttributeResolver;

    /**
     * SwatchRenderer constructor.
     * @param Context $context
     * @param Attribute $eavAttribute
     * @param AttributeFactory $layerAttribute
     * @param Data $swatchHelper
     * @param Media $mediaHelper
     * @param EavAttributeFactory $eavAttributeFactory
     * @param FilterHelper $filterHelper
     * @param SwatchAttributeResolver $swatchAttributeResolver
     * @param array $data
     */
    public function __construct(
        Context $context,
        Attribute $eavAttribute,
        AttributeFactory $layerAttribute,
        Data $swatchHelper,
        Media $mediaHelper,
        EavAttributeFactory $eavAttributeFactory,
        FilterHelper $filterHelper,
        SwatchAttributeResolver $swatchAttributeResolver,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $eavAttribute,
            $layerAttribute,
            $swatchHelper,
            $mediaHelper,
            $data
        );
        $this->eavAttributeFactory = $eavAttributeFactory;
        $this->filterHelper = $filterHelper;
        $this->swatchAttributeResolver = $swatchAttributeResolver;
    }

    /**
     * @param Filter $filter
     * @throws LocalizedException
     */
    public function setFilter(Filter $filter)
    {
        $this->filter = $filter;
        // Make sure attribute model exists
        if (!$this->filter->getAttributeModel()) {
            $attributeCode = $filter->getFacet()->getFacetSettings()->getUrlKey();
            $attributeModel = $this->eavAttributeFactory->create([]);
            $attributeModel->loadByCode(Product::ENTITY, $attributeCode);
            $this->filter->setAttributeModel($attributeModel);
        }
        $this->setSwatchFilter($filter);
    }

    /**
     * @return array
     */
    public function getSwatchData()
    {
        if (false === $this->eavAttribute instanceof Attribute) {
            throw new \RuntimeException('Magento_Swatches: RenderLayered: Attribute has not been set.');
        }

        /**
         * When this attribute has an id it is an actual magento attribute. If so we can use the parent method to
         * get the swatches, otherwise it is a mocked attribute see:
         * @see \Emico\Tweakwise\Model\Catalog\Layer\FilterList\Tweakwise line 105
        */
        if ($this->eavAttribute->getId()) {
            return parent::getSwatchData();
        }

        // We have a derived swatch filter.
        $swatchAttributeData = $this->swatchAttributeResolver->getSwatchData($this->filter);
        // There was no attribute to be found
        if (!$swatchAttributeData) {
            return parent::getSwatchData();
        }

        /** @var AttributeInterface|Attribute $attribute */
        $attribute = $swatchAttributeData['attribute'];
        $this->filter->setAttributeModel($attribute);
        $optionIds = array_values($swatchAttributeData['options']);
        $optionLabels = array_keys($swatchAttributeData['options']);

        $filterItems = [];
        foreach ($this->filter->getItems() as $item) {
            if (!in_array($item->getLabel(), $optionLabels, false)) {
                continue;
            }

            $filterItems[$item->getLabel()] = $item;
        }


        $attributeOptions = [];
        foreach ($attribute->getOptions() as $option) {
            if (!in_array($option->getValue(), $optionIds, false)) {
                continue;
            }

            $filterItem = $filterItems[$option->getLabel()] ?? null;
            if (!$filterItem) {
                continue;
            }

            $attributeOptions[$option->getValue()] = $this->getOptionViewData($filterItem, $option);
        }

        return [
            'attribute_id' => $attribute->getId(),
            'attribute_code' => $attribute->getAttributeCode(),
            'attribute_label' => $this->filter->getFacet()->getFacetSettings()->getTitle(),
            'options' => $attributeOptions,
            'swatches' => $this->swatchHelper->getSwatchesByOptionsId($optionIds),
        ];
    }

    /**
     * @param int $id
     * @return Item
     */
    public function getItemForSwatch($id)
    {
        return $this->filter->getItemByOptionId($id);
    }

    /**
     * @return SettingsType
     */
    protected function getFacetSettings()
    {
        return $this->filter->getFacet()->getFacetSettings();
    }

    /**
     * @return bool
     */
    public function shouldDisplayProductCountOnLayer()
    {
        return $this->getFacetSettings()->getIsNumberOfResultVisible();
    }
}
