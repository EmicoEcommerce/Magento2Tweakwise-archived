<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Block\LayeredNavigation\RenderLayered;

use Emico\Tweakwise\Model\Config;
use Emico\Tweakwise\Model\NavigationConfig;
use Emico\Tweakwise\Model\Seo\FilterHelper;
use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Serialize\Serializer\Json;

class SliderRenderer extends DefaultRenderer
{
    /**
     * {@inheritDoc}
     */
    protected $_template = 'Emico_Tweakwise::product/layered/slider.phtml';

    /**
     * @var PriceHelper
     */
    protected $priceHelper;

    /**
     * @var TaxHelper
     */
    protected $taxHelper;

    /**
     * SliderRenderer constructor.
     * @param PriceHelper $priceHelper
     * @param TaxHelper $taxHelper
     * @param Config $config
     * @param NavigationConfig $navigationConfig
     * @param FilterHelper $filterHelper
     * @param Template\Context $context
     * @param Json $jsonSerializer
     * @param array $data
     */
    public function __construct(
        PriceHelper $priceHelper,
        TaxHelper $taxHelper,
        Config $config,
        NavigationConfig $navigationConfig,
        FilterHelper $filterHelper,
        Template\Context $context,
        Json $jsonSerializer,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $config,
            $navigationConfig,
            $filterHelper,
            $jsonSerializer,
            $data
        );
        $this->priceHelper = $priceHelper;
        $this->taxHelper = $taxHelper;
    }

    /**
     * @param int $index
     * @param int|float $default
     * @return int|float|string
     */
    protected function getItemValue($index, $default = 0)
    {
        $items = $this->getItems();
        if (!isset($items[$index])) {
            return $default;
        }

        return (float) $items[$index]->getLabel();
    }

    /**
     * @return int
     */
    public function getMinValue()
    {
        return floor($this->getItemValue(2, $this->getCurrentMinValue()));
    }

    /**
     * @return int
     */
    public function getMaxValue()
    {
        return ceil($this->getItemValue(3, $this->getCurrentMaxValue()));
    }

    /**
     * @return int
     */
    public function getCurrentMinValue()
    {
        return floor($this->getItemValue(0));
    }

    /**
     * @return int
     */
    public function getCurrentMaxValue()
    {
        return ceil($this->getItemValue(1, 99999));
    }

    /**
     * @param string $value
     * @return float|string
     */
    public function renderValue($value)
    {
        if (!$this->filter->getFacet()->getFacetSettings()->isPrice()) {
            return $value;
        }

        return $this->priceHelper->currency($value);
    }

    /**
     * @return string
     */
    public function getPriceFormatJson()
    {
        return $this->taxHelper->getPriceFormat();
    }

    /**
     * @return string
     */
    public function getFilterUrl()
    {
        $items = $this->getItems();
        if (!isset($items[0])) {
            return '#';
        }

        return $items[0]->getUrl();
    }

    /**
     * @return bool|string
     */
    public function getJsUseFormFilters()
    {
        return $this->jsonSerializer->serialize($this->config->isFormFilters());
    }

    /**
     * @return string
     */
    public function getJsSliderConfig(): string
    {
        return $this->navigationConfig->getJsSliderConfig($this);
    }
}
