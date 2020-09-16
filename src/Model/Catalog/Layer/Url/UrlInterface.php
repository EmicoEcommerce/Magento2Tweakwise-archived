<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2019 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\Url;

use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Magento\Framework\App\Request\Http as MagentoHttpRequest;

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
     * @param MagentoHttpRequest $request
     * @param Item $item
     * @return string
     */
    public function getAttributeSelectUrl(MagentoHttpRequest $request, Item $item): string;

    /**
     * Get url when removing item from selecting
     *
     * @param MagentoHttpRequest $request
     * @param Item $item
     * @return string
     */
    public function getAttributeRemoveUrl(MagentoHttpRequest $request, Item $item): string;

    /**
     * @param MagentoHttpRequest $request
     * @param Item $item
     * @return string
     */
    public function getSliderUrl(MagentoHttpRequest $request, Item $item): string;

    /**
     * Fetch clear all items from url
     *
     * @param MagentoHttpRequest $request
     * @param Item[] $activeFilterItems
     * @return string
     */
    public function getClearUrl(MagentoHttpRequest $request, array $activeFilterItems): string;

    /**
     * @param MagentoHttpRequest $request
     * @param array $filters
     * @return string
     */
    public function buildFilterUrl(MagentoHttpRequest $request, array $filters = []): string;

    /**
     * Determine if this UrlInterface is allowed in the current context
     *
     * @return boolean
     */
    public function isAllowed(): bool;
}
