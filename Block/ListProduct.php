<?php

namespace Emico\Tweakwise\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Helper\Output as OutputHelper;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Url\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Magento\Msrp\Model\Config;

/**
 * Class Popup
 * @package Emico\Tweakwise\Block\Theme\Html\Pager
 */
class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * @var FormKey
     */
    protected $formKey;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        FormKey $formKey,
        array $data = [],
        ?OutputHelper $outputHelper = null
    )
    {
        $this->formKey = $formKey;
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data, $outputHelper);
    }

    /**
     * @return string
     * @throws LocalizedException
     * @return string
     */
    public function getFormKey(): string
    {
        return $this->formKey->getFormKey();
    }
}
