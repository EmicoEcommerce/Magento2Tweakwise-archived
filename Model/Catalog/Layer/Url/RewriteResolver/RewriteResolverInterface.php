<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\Url\RewriteResolver;

use Magento\Framework\App\Request\Http as MagentoHttpRequest;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

interface RewriteResolverInterface
{
    /**
     * This should extract rewrites based on a magento request.
     * Example: given that the pathslug strategy is active one expects urls like
     * /category/path/filter1/value1/filter2/value2.
     * This method should find the rewrites for the category entity,
     * it should return the rewrites corresponding to /category/path.
     *
     * The path slug strategy uses this information to set the filter path
     * which is used to query tweakwise and resolve the correct entity to load.
     *
     * @param MagentoHttpRequest $request
     * @return UrlRewrite[]
     * @see \Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy\PathSlugStrategy::match()
     */
    public function getRewrites(MagentoHttpRequest $request): array;
}
