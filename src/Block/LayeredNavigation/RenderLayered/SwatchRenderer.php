<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Block\LayeredNavigation\RenderLayered;

use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Layer\Filter\AttributeFactory;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\View\Element\Template\Context;
use Magento\Swatches\Block\LayeredNavigation\RenderLayered;
use Emico\Tweakwise\Model\Catalog\Layer\Filter;
use Magento\Swatches\Helper\Data;
use Magento\Swatches\Helper\Media;
use Emico\Tweakwise\Model\Config;
use Emico\Tweakwise\Model\Seo\FilterHelper;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory as EavAttributeFactory;

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
     * @var Config
     */
    protected $config;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var EavAttributeFactory
     */
    protected $eavAttributeFactory;

    /**
     * SwatchRenderer constructor.
     * @param Context $context
     * @param Attribute $eavAttribute
     * @param AttributeFactory $layerAttribute
     * @param Data $swatchHelper
     * @param Media $mediaHelper
     * @param Config $config
     * @param EavAttributeFactory $attributeFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Attribute $eavAttribute,
        AttributeFactory $layerAttribute,
        Data $swatchHelper,
        Media $mediaHelper,
        Config $config,
        EavAttributeFactory $eavAttributeFactory,
        FilterHelper $filterHelper,
        array $data = []
    )
    {
        parent::__construct($context, $eavAttribute, $layerAttribute, $swatchHelper, $mediaHelper, $data);
        $this->config = $config;
        $this->eavAttributeFactory = $eavAttributeFactory;
        $this->filterHelper = $filterHelper;
    }

    /**
     * @param Filter $filter
     * @throws \Magento\Framework\Exception\LocalizedException
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
     * @return bool
     */
    public function getUseFormFilters()
    {
        return $this->config->getUseFormFilters();
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
     * @param int $id
     * @return Item
     */
    public function getItemForSwatch($id)
    {
        return $this->filter->getItemByOptionId($id);
    }

    /**
     * @return string
     */
    public function getJsNavigationConfig()
    {
        return $this->config->getJsNavigationConfig();
    }
}