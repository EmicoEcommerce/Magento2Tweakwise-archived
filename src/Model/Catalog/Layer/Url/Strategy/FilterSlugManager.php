<?php

namespace Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy;

use Magento\Catalog\Model\Layer\Filter\Item;
use Magento\Framework\Filter\TranslitUrl;

/**
 * @author Bram Gerritsen <bgerritsen@emico.nl>
 * @copyright (c) Emico B.V. 2017
 */

class FilterSlugManager
{
    /**
     * @var TranslitUrl
     */
    private $translitUrl;

    /**
     * FilterSlugManager constructor.
     * @param TranslitUrl $translitUrl
     */
    public function __construct(TranslitUrl $translitUrl)
    {
        $this->translitUrl = $translitUrl;
    }

    /**
     * @param Item $filterItem
     * @return string
     */
    public function getSlugForFilterItemAttribute(Item $filterItem): string
    {
        $attribute = $filterItem->getAttribute()->getTitle();
        return $this->translitUrl->filter($attribute);
    }
}