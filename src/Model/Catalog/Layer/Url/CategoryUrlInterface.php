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
 * Interface UrlInterface implementation should handle both category url's.
 */
interface CategoryUrlInterface
{
    /**
     * @param MagentoHttpRequest $request
     * @param Item $item
     * @return mixed
     */
    public function getCategoryFilterSelectUrl(MagentoHttpRequest $request, Item $item): string;

    /**
     * @param MagentoHttpRequest $request
     * @param Item $item
     * @return mixed
     */
    public function getCategoryFilterRemoveUrl(MagentoHttpRequest $request, Item $item): string;
}
