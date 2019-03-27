<?php
/**
 * @author Bram Gerritsen <bgerritsen@emico.nl>
 * @copyright (c) Emico B.V. 2017
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