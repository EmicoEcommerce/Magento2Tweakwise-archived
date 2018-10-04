<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Block\LayeredNavigation\RenderLayered;

use Emico\Tweakwise\Model\Config;
use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\View\Element\Template;

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
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(PriceHelper $priceHelper, TaxHelper $taxHelper, Config $config, Template\Context $context, array $data = [])
    {
        parent::__construct($context, $config, $data);
        $this->priceHelper = $priceHelper;
        $this->taxHelper = $taxHelper;
    }

    /**
     * @param int $index
     * @param int $default
     * @return int
     */
    protected function getItemIntValue($index, $default = 0)
    {
        return (int) $this->getItemValue($index, $default);
    }

    /**
     * @param int $index
     * @param float $default
     * @return float
     */
    protected function getItemFloatValue($index, $default = 0.0)
    {
        return (float) $this->getItemValue($index, $default);
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

        return $items[$index]->getLabel();
    }

    /**
     * @return int
     */
    public function getMinValue()
    {
        return $this->getItemIntValue(2, $this->getCurrentMinValue());
    }

    /**
     * @return int
     */
    public function getMaxValue()
    {
        return $this->getItemIntValue(3, $this->getCurrentMaxValue());
    }

    /**
     * @return float
     */
    public function getMaxFloatValue()
    {
        return $this->getItemFloatValue(3, $this->getCurrentMaxFloatValue());
    }

    /**
     * @return int
     */
    public function getCurrentMinValue()
    {
        return $this->getItemIntValue(0);
    }

    /**
     * @return int
     */
    public function getCurrentMaxValue()
    {
        return $this->getItemIntValue(1, 99999);
    }

    /**
     * @return float
     */
    public function getCurrentMaxFloatValue()
    {
        return $this->getItemFloatValue(1, 99999);
    }

    /**
     * @param int $value
     * @return string
     */
    public function renderPrice($value)
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
    public function getPriceUrl()
    {
        $items = $this->getItems();
        if (!isset($items[0])) {
            return '#';
        }

        return $items[0]->getUrl();
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }
}