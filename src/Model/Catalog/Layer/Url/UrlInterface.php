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
use Magento\Framework\Api\SortOrder;
use Zend\Http\Request as HttpRequest;

/**
 * Interface UrlInterface implementation should handle both category url's and
 *
 *
 */
interface UrlInterface
{
    /**
     * Get url when selecting item
     *
     * @param HttpRequest $request
     * @param Item $item
     * @return string
     */
    public function getSelectFilter(HttpRequest $request, Item $item);

    /**
     * Get url when removing item from selecting
     *
     * @param HttpRequest $request
     * @param Item $item
     * @return string
     */
    public function getRemoveFilter(HttpRequest $request, Item $item);

    /**
     * @param HttpRequest $request
     * @param Filter $filter
     * @return string
     */
    public function getSlider(HttpRequest $request, Filter $filter);

    /**
     * Fetch clear all items from url
     *
     * @param HttpRequest $request
     * @param Item[] $activeFilterItems
     * @return string
     */
    public function getClearUrl(HttpRequest $request, array $activeFilterItems);
}