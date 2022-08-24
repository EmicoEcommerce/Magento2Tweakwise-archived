<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Autocomplete\DataProvider;

use Magento\Catalog\Block\Product\Image;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Model\Product;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Catalog\Model\Product\Type;
use Magento\Search\Model\Autocomplete\ItemInterface;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class ProductItem
 * @package Emico\Tweakwise\Model\Autocomplete\DataProvider
 */
class ProductItem implements ItemInterface
{
    /**
     * @var Product
     */
    protected $product;

    /**
     * @var ImageFactory
     */
    protected $imageFactory;

    /**
     * @var ImageBuilder
     */
    protected $imageBuilder;

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * ProductItem constructor.
     *
     * @param Product $product
     * @param ImageFactory $imageFactory
     * @param ImageBuilder $imageBuilder
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        Product $product,
        ImageFactory $imageFactory,
        ImageBuilder $imageBuilder,
        ProductMetadataInterface $productMetadata
    ) {
        $this->product = $product;
        $this->imageFactory = $imageFactory;
        $this->imageBuilder = $imageBuilder;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->product->getName();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $product = $this->product;
        $priceInfo = $product->getPriceInfo();
        $productType = $product->getTypeId();
        $image = $this->getImage();

        $price = (float) $priceInfo->getPrice('regular_price')->getValue();

        if ($productType == Grouped::TYPE_CODE || $productType == Type::TYPE_BUNDLE) {
            $price = (float) $product->getData('tweakwise_price');
        }

        return [
            'title' => $this->getTitle(),
            'url' => $product->getProductUrl(),
            'image' => $image->getImageUrl(),
            'price' => $price,
            'final_price' => (float) $priceInfo->getPrice('final_price')->getValue(),
            'type' => 'product',
            'row_class' => 'qs-option-product',
        ];
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * ImageFactory class does not exist in 2.2 so we need a proxy
     *
     * @return Image
     */
    protected function getImage(): Image
    {
        $version = $this->productMetadata->getVersion();
        if (version_compare($version, '2.3.0', '<')) {
            $imageResolver = $this->imageBuilder;
            $imageResolver->setProduct($this->product);
            $imageResolver->setImageId('product_thumbnail_image');
            return $imageResolver->create();
        }

        $imageResolver = $this->imageFactory;

        return $imageResolver->create(
            $this->product,
            'product_thumbnail_image',
            []
        );
    }
}
