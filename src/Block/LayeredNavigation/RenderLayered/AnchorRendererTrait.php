<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */

namespace Emico\Tweakwise\Block\LayeredNavigation\RenderLayered;

use Emico\Tweakwise\Model\Seo\FilterHelper;
use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;

trait AnchorRendererTrait
{
    /**
     * @var FilterHelper
     */
    protected $filterHelper;

    /**
     * @param Item $item
     * @return string
     */
    public function renderAnchorHtmlTagAttributes(Item $item)
    {
        $anchorAttributes = $this->getAnchorTagAttributes($item);
        $attributeHtml = [];
        foreach ($anchorAttributes as $anchorAttribute => $anchorAttributeValue) {
            $attributeHtml[] = sprintf('%s="%s"', $anchorAttribute, $anchorAttributeValue);
        }
        return implode(' ', $attributeHtml);
    }

    /**
     * @param Item $item
     * @return string[]
     */
    protected function getAnchorTagAttributes(Item $item): array
    {
        $itemUrl = $this->getItemUrl($item);
        if ($this->filterHelper->shouldFilterBeIndexable($item)) {
            return ['href' => $itemUrl];
        }

        return ['href' => '#', 'data-seo-href' => $itemUrl];
    }

    /**
     * @param Item $item
     * @return string
     */
    protected function getItemUrl(Item $item)
    {
        return $this->escapeHtml($item->getUrl());
    }
}