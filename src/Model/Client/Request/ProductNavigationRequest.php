<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Client\Request;

use Emico\Tweakwise\Model\Client\Request;
use Emico\Tweakwise\Model\Client\Response\ProductNavigationResponse;
use Magento\Catalog\Model\Category;

class ProductNavigationRequest extends Request
{
    /**
     * {@inheritDoc}
     */
    protected $path = 'navigation';

    /**
     * {@inheritdoc}
     */
    public function getResponseType()
    {
        return ProductNavigationResponse::class;
    }

    /**
     * @param Category $category
     * @return $this
     */
    public function addCategoryFilter(Category $category)
    {
        $this->addParameter('tn_cid', $this->helper->getTweakwiseId($category->getStoreId(), $category->getId()), '-');
        return $this;
    }
}