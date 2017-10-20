<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Client\Request\Recommendation;

use Emico\Tweakwise\Exception\ApiException;
use Emico\TweakwiseExport\Model\Helper;
use Magento\Catalog\Model\Product;
use Magento\Store\Model\StoreManager;

class ProductRequest extends FeaturedRequest
{
    /**
     * {@inheritDoc}
     */
    protected $path = 'recommendations/product';

    /**
     * @var Product
     */
    protected $product;

    public function __construct(Helper $helper, StoreManager $storeManager)
    {
        parent::__construct($helper, $storeManager);
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return $this
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPathSuffix()
    {
        if (!$this->product) {
            throw new ApiException('Featured products without product was requested.');
        }
        return  parent::getPathSuffix() . '/' . $this->helper->getTweakwiseId($this->product->getStoreId(), $this->product->getId());
    }
}