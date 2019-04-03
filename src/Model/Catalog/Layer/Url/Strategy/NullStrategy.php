<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2019 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy;


use Emico\Tweakwise\Model\Catalog\Layer\Filter;
use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Emico\Tweakwise\Model\Catalog\Layer\Url\FilterApplierInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\RouteMatchingInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\UrlInterface;
use Emico\Tweakwise\Model\Client\Request\ProductNavigationRequest;
use Magento\Framework\App\ActionInterface;
use Zend\Http\Request as HttpRequest;
use Magento\Framework\App\Request\Http as MagentoHttpRequest;

class NullStrategy implements RouteMatchingInterface, UrlInterface, FilterApplierInterface
{
    /**
     * @param MagentoHttpRequest $request
     * @return bool|ActionInterface
     */
    public function match(MagentoHttpRequest $request)
    {
        return false;
    }

    /**
     * Apply all attribute filters, category filters, sort order, page limit request parameters to navigation request
     *
     * @param HttpRequest $request
     * @param ProductNavigationRequest $navigationRequest
     * @return $this
     */
    public function apply(HttpRequest $request, ProductNavigationRequest $navigationRequest)
    {
        return $this;
    }

    /**
     * Get url when selecting item
     *
     * @param HttpRequest $request
     * @param Item $item
     * @return string
     */
    public function getSelectFilter(HttpRequest $request, Item $item)
    {
        return '';
    }

    /**
     * Get url when removing item from selecting
     *
     * @param HttpRequest $request
     * @param Item $item
     * @return string
     */
    public function getRemoveFilter(HttpRequest $request, Item $item)
    {
        return '';
    }

    /**
     * @param HttpRequest $request
     * @param Filter $filter
     * @return string
     */
    public function getSlider(HttpRequest $request, Filter $filter)
    {
        return '';
    }

    /**
     * Fetch clear all items from url
     *
     * @param HttpRequest $request
     * @param Item[] $activeFilterItems
     * @return string
     */
    public function getClearUrl(HttpRequest $request, array $activeFilterItems)
    {
        return '';
    }
}