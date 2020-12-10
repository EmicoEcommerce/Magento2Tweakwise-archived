<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\MagentoCompat;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class PreparePostDataResolver
 * @package Emico\Tweakwise\MagentoCompat
 * Unfortunately class \Magento\Catalog\ViewModel\Product\Listing\PreparePostData
 * does not exist in magento 2.3 but is used in magento 2.4.
 * Magento_Catalog::product/list/items.phtml line 265 wants a PreparePostData from the block rendering the template
 * PreparePostDataResolver tries to get an instance of that class if it is available, if it is
 * we add it as a view model so that we remain compatible with magento 2.3 and lower.
 */
class PreparePostDataFactory
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * PreparePostDataResolver constructor.
     * @param ObjectManagerInterface $objectManager
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ProductMetadataInterface $productMetadata
    ) {
        $this->objectManager = $objectManager;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @return mixed|null
     */
    public function getPreparePostData(): ?ArgumentInterface
    {
        /** @noinspection ClassConstantCanBeUsedInspection */
        return $this->isMagento24() && \class_exists('\\Magento\\Catalog\\ViewModel\\Product\\Listing\\PreparePostData')
            ? $this->objectManager->get('\\Magento\\Catalog\\ViewModel\\Product\\Listing\\PreparePostData')
            : null;
    }

    /**
     * @return bool
     */
    protected function isMagento24(): bool
    {
        $magentoVersion = $this->productMetadata->getVersion();
        return version_compare($magentoVersion, '2.4', '>=');
    }
}
