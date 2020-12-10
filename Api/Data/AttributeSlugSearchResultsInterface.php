<?php
/**
 * @author Bram Gerritsen <bgerritsen@emico.nl>
 * @copyright (c) Emico B.V. 2019
 */

namespace Emico\Tweakwise\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface AttributeSlugSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return AttributeSlugInterface[]
     */
    public function getItems(): array;
}
