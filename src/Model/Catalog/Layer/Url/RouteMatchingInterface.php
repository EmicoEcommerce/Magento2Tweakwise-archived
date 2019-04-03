<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2019 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\Url;


use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\Http as MagentoHttpRequest;

interface RouteMatchingInterface
{
    /**
     * @param MagentoHttpRequest $request
     * @return bool|ActionInterface
     */
    public function match(MagentoHttpRequest $request);
}