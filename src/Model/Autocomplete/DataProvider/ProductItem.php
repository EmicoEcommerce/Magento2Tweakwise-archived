<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Autocomplete\DataProvider;

use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Model\Product;
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
    public function getTitle()
    {
        return $this->product->getName();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $product = $this->product;
        $price = $product->getPriceInfo();
        $image = $this->getImage($product);

        return [
            'title' => $this->getTitle(),
            'url' => $product->getProductUrl(),
            'image' => $image->getImageUrl(),
            'price' => (float) $price->getPrice('regular_price')->getValue(),
            'final_price' => (float) $price->getPrice('final_price')->getValue(),
            'type' => 'product',
            'row_class' => 'qs-option-product',
        ];
    }

    /**
     * ImageFactory class does not exist in 2.2 so we need a proxy
     *
     * @param Product $product
     * @return \Magento\Catalog\Block\Product\Image
     */
    protected function getImage(Product $product)
    {
        $version = $this->productMetadata->getVersion();
        if (version_compare($version, '2.3.0', '<')) {
            $imageResolver = $this->imageBuilder;
        } else {
            $imageResolver = $this->imageFactory;
        }

        return $imageResolver->create(
            $product,
            'product_thumbnail_image',
            []
        );
    }
}
