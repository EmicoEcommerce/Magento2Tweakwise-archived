<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2019 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy;

use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Emico\Tweakwise\Model\Catalog\Layer\Url\FilterApplierInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\RouteMatchingInterface;
use Emico\Tweakwise\Model\Catalog\Layer\Url\UrlInterface;
use Emico\Tweakwise\Model\Client\Request\ProductNavigationRequest;
use Magento\Framework\App\ActionInterface;
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
     * @param MagentoHttpRequest $request
     * @param ProductNavigationRequest $navigationRequest
     * @return $this
     */
    public function apply(MagentoHttpRequest $request, ProductNavigationRequest $navigationRequest): FilterApplierInterface
    {
        return $this;
    }

    /**
     * Get url when selecting item
     *
     * @param MagentoHttpRequest $request
     * @param Item $item
     * @return string
     */
    public function getAttributeSelectUrl(MagentoHttpRequest $request, Item $item): string
    {
        return '';
    }

    /**
     * Get url when removing item from selecting
     *
     * @param MagentoHttpRequest $request
     * @param Item $item
     * @return string
     */
    public function getAttributeRemoveUrl(MagentoHttpRequest $request, Item $item): string
    {
        return '';
    }

    /**
     * @param MagentoHttpRequest $request
     * @param Item $item
     * @return string
     */
    public function getSliderUrl(MagentoHttpRequest $request, Item $item): string
    {
        return '';
    }

    /**
     * Fetch clear all items from url
     *
     * @param MagentoHttpRequest $request
     * @param Item[] $activeFilterItems
     * @return string
     */
    public function getClearUrl(MagentoHttpRequest $request, array $activeFilterItems): string
    {
        return '';
    }

    /**
     * Determine if this UrlInterface is allowed in the current context
     *
     * @return boolean
     */
    public function isAllowed(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function buildFilterUrl(MagentoHttpRequest $request, array $filters = []): string
    {
        return '';
    }
}
