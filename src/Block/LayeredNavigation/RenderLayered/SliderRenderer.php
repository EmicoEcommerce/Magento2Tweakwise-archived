<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Block\LayeredNavigation\RenderLayered;

use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\View\Element\Template;

class SliderRenderer extends AbstractRenderer
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
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(PriceHelper $priceHelper, TaxHelper $taxHelper, Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);
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
        $items = $this->getItems();
        if (!isset($items[$index])) {
            return $default;
        }

        return (int) $items[$index]->getLabel();
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
}