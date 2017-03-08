<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Client\Request;

use Emico\Tweakwise\Model\Client\Request;
use Magento\Catalog\Model\Category;

class ProductNavigation extends Request
{
    /**
     * {@inheritDoc}
     */
    protected $path = 'navigation';

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