<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2019 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\Url;

use Emico\Tweakwise\Model\Catalog\Layer\Filter;
use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Emico\Tweakwise\Model\Client\Request\ProductNavigationRequest;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\Api\SortOrder;
use Zend\Http\Request as HttpRequest;

/**
 * Interface UrlInterface implementation should handle both category url's and
 *
 *
 */
interface CategoryUrlInterface
{
    /**
     * @param HttpRequest $request
     * @param Item $item
     * @param CategoryInterface $category
     * @return string
     */
    public function getCategoryTreeSelectUrl(HttpRequest $request, Item $item, CategoryInterface $category): string;

    /**
     * @param HttpRequest $request
     * @param Item $item
     * @param CategoryInterface $category
     * @return mixed
     */
    public function getCategoryTreeRemoveUrl(HttpRequest $request, Item $item, CategoryInterface $category): string;

    /**
     * @param HttpRequest $request
     * @param Item $item
     * @param CategoryInterface $category
     * @return mixed
     */
    public function getCategoryFilterSelectUrl(HttpRequest $request, Item $item, CategoryInterface $category): string;

    /**
     * @param HttpRequest $request
     * @param Item $item
     * @param CategoryInterface $category
     * @return mixed
     */
    public function getCategoryFilterRemoveUrl(HttpRequest $request, Item $item, CategoryInterface $category): string;
}