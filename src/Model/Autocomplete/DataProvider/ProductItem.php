<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Autocomplete\DataProvider;

use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Model\Product;
use Magento\Search\Model\Autocomplete\ItemInterface;

class ProductItem implements ItemInterface
{
    /**
     * @var Product
     */
    protected $product;

    /**
     * @var ProductHelper
     */
    protected $productHelper;

    /**
     * ProductItem constructor.
     *
     * @param Product $product
     * @param ProductHelper $productHelper
     */
    public function __construct(Product $product, ProductHelper $productHelper)
    {
        $this->product = $product;
        $this->productHelper = $productHelper;
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
        $productHelper = $this->productHelper;

        return [
            'name' => $product->getName(),
            'url' => $product->getProductUrl(),
            'image' => $productHelper->getSmallImageUrl($product),
        ];
    }
}