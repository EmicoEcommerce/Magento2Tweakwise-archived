<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Type\SuggestionType;

use Emico\TweakwiseExport\Model\Helper;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class SuggestionTypeFacet extends SuggestionTypeCategory
{
    /**
     * @return string
     */
    public function getUrl(): string
    {
        $categoryUrl = $this->getCategoryUrl();
        $categoryIds = $this->getCategoryIds();
        $facets = $this->getFacets();
        if (!$categoryUrl) {
            return '';
        }

        // Add facets here
        return $categoryUrl;
    }

    /**
     *
     */
    protected function getFacets()
    {
        $facets = $this->data['navigationLink']['context']['facetFilters']['filter'] ?: [];
        $keys = array_column($facets, 'key');
        $values = array_column($facets, 'values');

        return array_combine($keys, $values);
    }
}
